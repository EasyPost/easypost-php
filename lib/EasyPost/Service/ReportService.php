<?php

namespace EasyPost\Service;

use EasyPost\Constant\Constants;
use EasyPost\Exception\General\EndOfPaginationException;
use EasyPost\Exception\General\MissingParameterException;
use EasyPost\Http\Requestor;
use EasyPost\Util\InternalUtil;

/**
 * Report service containing all the logic to make API calls.
 */
class ReportService extends BaseService
{
    /**
     * Retrieve a report.
     *
     * @param string $id
     * @return mixed
     */
    public function retrieve(string $id): mixed
    {
        return self::retrieveResource(self::serviceModelClassName(self::class), $id);
    }

    /**
     * Retrieve all reports.
     *
     * @param mixed $params
     * @return mixed
     * @throws MissingParameterException
     */
    public function all(mixed $params = null): mixed
    {
        if (!isset($params) || !isset($params['type'])) {
            throw new MissingParameterException(Constants::MISSING_PARAMETER_ERROR);
        } else {
            $type = $params['type'];
            self::validate($params);
            $url = self::reportUrl($type);
            unset($params['type']);
            $response = Requestor::request($this->client, 'get', $url, $params);
            $response['type'] = $type;

            return InternalUtil::convertToEasyPostObject($this->client, $response);
        }
    }

    /**
     * Retrieve the next page of Report collection
     *
     * @param mixed $reports
     * @param int|null $pageSize
     * @return mixed
     */
    public function getNextPage(mixed $reports, ?int $pageSize = null): mixed
    {
        $reportArray = $reports['reports'];
        $userParams = $reports['_params'] ?? null;

        if (empty($reportArray) || !$reports['has_more']) {
            throw new EndOfPaginationException();
        }

        $params = [
            'page_size' => $pageSize,
            'before_id' => $reportArray[count($reportArray) - 1]['id']
        ];

        if (isset($userParams)) {
            $params = array_merge($params, $userParams);
        }

        $url = self::reportUrl($reportArray[0]->object);
        $response = Requestor::request($this->client, 'get', $url, $params);

        if (isset($userParams)) {
            $response['_params'] = $userParams;
        }

        if (empty($response['reports'])) {
            throw new EndOfPaginationException();
        }

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Create a report.
     *
     * @param mixed $params
     * @return mixed
     * @throws MissingParameterException
     */
    public function create(mixed $params = null): mixed
    {
        if (!isset($params['type'])) {
            throw new MissingParameterException(Constants::MISSING_PARAMETER_ERROR);
        }

        $url = self::reportUrl($params['type']);
        self::validate($params);
        $response = Requestor::request($this->client, 'post', $url, $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Generate report url format.
     *
     * @param string $type
     * @return mixed
     */
    private function reportUrl(string $type): mixed
    {
        // Strip namespace if present
        if ($postfix = strrchr($type, '\\')) {
            $type = substr($postfix, 1);
        }
        if (substr($type, 0, strlen('EasyPost')) == 'EasyPost') {
            $type = substr($type, strlen('EasyPost'));
        }

        $type = substr($type, 0, 1) . preg_replace('/([A-Z])/', '_$1', substr($type, 1)); // Camel -> snake

        $type = strtolower($type);
        $type = str_replace('_report', '', $type);
        $name = urlencode($type);

        return "/reports/{$name}";
    }
}
