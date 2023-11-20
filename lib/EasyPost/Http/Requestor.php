<?php

namespace EasyPost\Http;

use DateTime;
use DateTimeZone;
use EasyPost\Constant\Constants;
use EasyPost\EasyPostClient;
use EasyPost\EasypostObject;
use EasyPost\Exception\Api\BadRequestException;
use EasyPost\Exception\Api\ForbiddenException;
use EasyPost\Exception\Api\GatewayTimeoutException;
use EasyPost\Exception\Api\HttpException;
use EasyPost\Exception\Api\InternalServerException;
use EasyPost\Exception\Api\InvalidRequestException;
use EasyPost\Exception\Api\JsonException;
use EasyPost\Exception\Api\MethodNotAllowedException;
use EasyPost\Exception\Api\NotFoundException;
use EasyPost\Exception\Api\PaymentException;
use EasyPost\Exception\Api\RateLimitException;
use EasyPost\Exception\Api\RedirectException;
use EasyPost\Exception\Api\ServiceUnavailableException;
use EasyPost\Exception\Api\TimeoutException;
use EasyPost\Exception\Api\UnauthorizedException;
use EasyPost\Exception\Api\UnknownApiException;
use GuzzleHttp\Client;

class Requestor
{
    /**
     * Get the API URL.
     *
     * @param EasyPostClient $client
     * @param string $url
     * @param bool $beta
     * @return string
     */
    private static function absoluteUrl($client, $url = '', $beta = false)
    {
        if ($beta) {
            $apiBase = Constants::API_BASE . '/' . Constants::BETA_API_VERSION;
        } else {
            $apiBase = $client->getApiBase() . '/' . Constants::API_VERSION;
        }

        return "{$apiBase}{$url}";
    }

    /**
     * Converts a set of values to UTF-8 encoding.
     *
     * @param mixed $value
     * @return string
     */
    public static function utf8($value)
    {
        if (is_string($value) && mb_detect_encoding($value, 'UTF-8', true) != 'UTF-8') {
            return mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1');
        }

        return $value;
    }

    /**
     * Encodes an EasyPost object and prepares the data for the request.
     *
     * @param mixed $data
     * @return array|string
     */
    private static function encodeObjects($data)
    {
        if (is_null($data)) {
            return [];
        } elseif ($data instanceof EasypostObject) {
            return ['id' => self::utf8($data->id)];
        } elseif ($data === true) {
            return 'true';
        } elseif ($data === false) {
            return 'false';
        } elseif (is_array($data)) {
            $resource = [];
            foreach ($data as $k => $v) {
                if (!is_null($v) and ($v !== '') and (!is_array($v) or !empty($v))) {
                    $resource[$k] = self::encodeObjects($v);
                }
            }

            return $resource;
        } else {
            return self::utf8(strval($data));
        }
    }

    /**
     * URL Encodes data for GET requests.
     *
     * @param mixed $arr
     * @param null $prefix
     * @return string
     */
    public static function urlEncode($arr, $prefix = null)
    {
        if (!is_array($arr)) {
            return $arr;
        }

        $r = [];
        foreach ($arr as $k => $v) {
            if (is_null($v)) {
                continue;
            }

            if ($prefix && isset($k)) {
                $k = $prefix . '[' . $k . ']';
            } elseif ($prefix) {
                $k = $prefix . '[]';
            }

            if (is_array($v)) {
                $r[] = self::urlEncode($v, $k);
            } else {
                $r[] = urlencode($k) . '=' . urlencode($v);
            }
        }

        return implode('&', $r);
    }

    /**
     * Make a request to the EasyPost API.
     *
     * @param EasyPostClient $client
     * @param string $method
     * @param string $url
     * @param mixed $params
     * @param bool $beta
     * @return array
     */
    public static function request($client, $method, $url, $params = null, $beta = false)
    {
        list($responseBody, $httpStatus) = self::requestRaw($client, $method, $url, $params, $beta);
        $httpBody = self::interpretResponse($responseBody, $httpStatus);

        return $httpBody;
    }

    /**
     * Internal logic required to make a request to the EasyPost API.
     *
     * @param EasyPostClient $client
     * @param string $method
     * @param string $url
     * @param mixed $params
     * @param bool $beta
     * @return array
     * @throws HttpException
     * @throws TimeoutException
     */
    private static function requestRaw($client, $method, $url, $params, $beta = false)
    {
        $absoluteUrl = self::absoluteUrl($client, $url, $beta);
        $requestOptions = [
            'http_errors' => false, // we set this false here so we can do our own error handling
            'timeout' => $client->getTimeout(),
        ];
        if (in_array(strtolower($method), ['get', 'delete'])) {
            $requestOptions['query'] = $params;
        } else {
            $params = self::encodeObjects($params);
            $requestOptions['json'] = $params;
        }

        $phpVersion = phpversion();
        $osType = php_uname('s');
        $osVersion = php_uname('r');
        $osArch = php_uname('m');

        $headers = [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$client->getApiKey()}",
            'Content-Type' => 'application/json',
            'User-Agent' => 'EasyPost/v2 PhpClient/' . Constants::LIBRARY_VERSION . " PHP/$phpVersion OS/$osType OSVersion/$osVersion OSArch/$osArch", // phpcs:ignore
        ];

        $requestUuid = uniqid();
        $requestTimestamp = (float) (new DateTime('now', new DateTimeZone('UTC')))->format('U.u');
        ($client->requestEvent)([
            'method' => $method,
            'path' => $absoluteUrl,
            'headers' => $headers,
            'request_body' => $params,
            'request_timestamp' => $requestTimestamp,
            'request_uuid' => $requestUuid,
        ]);

        if ($client->mock()) {
            // If there are mock requests set, this client will ONLY make mock requests
            $mockingUtility = $client->getMockingUtility();
            $matchingRequest = $mockingUtility->findMatchingMockRequest($method, $absoluteUrl);
            if ($matchingRequest === null) {
                throw new HttpException(sprintf(Constants::NO_MATCHING_MOCK_REQUEST, $method, $absoluteUrl));
            }

            $responseBody = $matchingRequest->responseInfo->body;
            $httpStatus = $matchingRequest->responseInfo->statusCode;
            $responseHeaders = [];
        } else {
            $guzzleClient = new Client();
            $requestOptions['headers'] = $headers;
            try {
                $response = $guzzleClient->request($method, $absoluteUrl, $requestOptions);
            } catch (\GuzzleHttp\Exception\ConnectException $error) {
                throw new HttpException(sprintf(Constants::COMMUNICATION_ERROR, 'EasyPost', $error->getMessage()));
            }

            // Guzzle does not have a native way of catching timeout exceptions...
            // If we don't have a response at this point, it's likely due to a timeout.
            if (!isset($response)) {
                throw new TimeoutException(sprintf(Constants::NO_RESPONSE_ERROR, 'EasyPost'));
            }

            $responseBody = $response->getBody();
            $httpStatus = $response->getStatusCode();
            $responseHeaders = $response->getHeaders();
        }

        $responseTimestamp = (float) (new DateTime('now', new DateTimeZone('UTC')))->format('U.u');
        ($client->responseEvent)([
            'http_status' => $httpStatus,
            'method' => $method,
            'path' => $absoluteUrl,
            'headers' => $responseHeaders,
            'response_body' => $responseBody,
            'request_timestamp' => $requestTimestamp,
            'response_timestamp' => $responseTimestamp,
            'request_uuid' => $requestUuid,
        ]);

        return [$responseBody, $httpStatus];
    }

    /**
     * Interpret the response body we receive from the API.
     *
     * @param string $httpBody
     * @param int $httpStatus
     * @return mixed
     * @throws JsonException
     */
    public static function interpretResponse($httpBody, $httpStatus)
    {
        try {
            $response = json_decode($httpBody, true);
        } catch (\Exception $e) {
            throw new JsonException(
                "Invalid response body from API: HTTP Status: ({$httpStatus}) {$httpBody}",
                $httpStatus,
                $httpBody
            );
        }

        if ($httpStatus < 200 || $httpStatus >= 300) {
            self::handleApiError($httpBody, $httpStatus, $response);
        }

        return $response;
    }

    /**
     * Handles API errors returned from EasyPost.
     *
     * @param string $httpBody
     * @param int $httpStatus
     * @param array $response
     * @throws BadRequestException
     * @throws GatewayTimeoutException
     * @throws InternalServerException
     * @throws InvalidRequestException
     * @throws JsonException
     * @throws MethodNotAllowedException
     * @throws NotFoundException
     * @throws PaymentException
     * @throws RateLimitException
     * @throws RedirectException
     * @throws ServiceUnavailableException
     * @throws TimeoutException
     * @throws UnauthorizedException
     * @throws UnknownApiException
     */
    public static function handleApiError($httpBody, $httpStatus, $response)
    {
        if (!is_array($response) || !isset($response['error'])) {
            throw new JsonException(
                "Invalid response object from API: HTTP Status: ({$httpStatus}) {$httpBody})",
                $httpStatus,
                $httpBody
            );
        }

        $errorMessageList = [];
        $errorMessage = $response['error']['message'];
        $message = is_string($errorMessage) ? $errorMessage :
            self::traverseJsonElement($errorMessage, $errorMessageList);

        if ($httpStatus >= 300 && $httpStatus < 400) {
            throw new RedirectException($message, $httpStatus, $httpBody);
        }

        switch ($httpStatus) {
            case 400:
                $errorType = BadRequestException::class;
                break;
            case 401:
                $errorType = UnauthorizedException::class;
                break;
            case 402:
                $errorType = PaymentException::class;
                break;
            case 403:
                $errorType = ForbiddenException::class;
                break;
            case 404:
                $errorType = NotFoundException::class;
                break;
            case 405:
                $errorType = MethodNotAllowedException::class;
                break;
            case 408:
                $errorType = TimeoutException::class;
                break;
            case 422:
                $errorType = InvalidRequestException::class;
                break;
            case 429:
                $errorType = RateLimitException::class;
                break;
            case 500:
                $errorType = InternalServerException::class;
                break;
            case 503:
                $errorType = ServiceUnavailableException::class;
                break;
            case 504:
                $errorType = GatewayTimeoutException::class;
                break;
            default:
                $errorType = UnknownApiException::class;
                break;
        }

        throw new $errorType($message, $httpStatus, $httpBody);
    }

    /**
     * Recursively traverses a JSON element to extract error messages and returns them as a comma-separated string.
     *
     * @param array $errorMessage
     * @param array $messagesList
     * @return string
     */
    private static function traverseJsonElement($errorMessage, &$messagesList)
    {
        switch (gettype($errorMessage)) {
            case 'array':
                foreach ($errorMessage as $value) {
                    self::traverseJsonElement($value, $messagesList);
                }
                break;
            case 'object':
                foreach ($errorMessage as $value) {
                    self::traverseJsonElement($value, $messagesList);
                }
                break;
            default:
                $messagesList[] = strval($errorMessage);
                break;
        }

        return implode(', ', $messagesList);
    }
}
