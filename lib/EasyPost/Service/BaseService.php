<?php

namespace EasyPost\Service;

use EasyPost\Constant\Constants;
use EasyPost\Exception\General\InvalidObjectException;
use EasyPost\Exception\General\InvalidParameterException;
use EasyPost\Exception\General\EndOfPaginationException;
use EasyPost\Http\Requestor;
use EasyPost\Util\InternalUtil;

use function PHPUnit\Framework\assertTrue;

class BaseService
{
    protected $client;

    /**
     * Service constructor shared by all child services.
     *
     * @param EasyPostClient $client
     */
    protected function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * The class name of an object.
     *
     * @param string $class
     * @return string
     */
    protected static function className($class)
    {
        // Strip namespace if present
        if ($postfix = strrchr($class, '\\')) {
            $class = substr($postfix, 1);
        }
        if (substr($class, 0, strlen('EasyPost')) == 'EasyPost') {
            $class = substr($class, strlen('EasyPost'));
        }
        $class = str_replace('_', '', $class);
        $class = substr($class, 0, 1) . preg_replace('/([A-Z])/', '_$1', substr($class, 1)); // Camel -> snake
        $name = urlencode($class);
        $name = strtolower($name);

        return $name;
    }

    /**
     * Gets the class name of a Service's model.
     *
     * @param string $serviceClassName
     * @return string
     */
    protected static function serviceModelClassName($serviceClassName)
    {
        return str_replace('Service', '', $serviceClassName);
    }

    /**
     * The class URL of an object.
     *
     * @param string $class
     * @return string
     */
    protected static function classUrl($class)
    {
        $className = self::className($class);
        if (substr($className, -1) !== 's' && substr($className, -1) !== 'h') {
            return "/{$className}s";
        }

        return "/{$className}es";
    }

    /**
     * The instance URL of an object.
     *
     * @param string $class
     * @param string $id
     * @return string
     * @throws InvalidObjectException
     */
    protected function instanceUrl($class, $id)
    {
        if (!$id) {
            throw new InvalidObjectException(sprintf(Constants::NO_ID_URL_ERROR), $class, $id);
        }
        $id = Requestor::utf8($id);
        $classUrl = self::classUrl($class);

        return "{$classUrl}/" . urlencode($id);
    }

    /**
     * Validate library usage.
     *
     * @param array $params
     * @throws InvalidParameterException
     */
    protected static function validate($params = null)
    {
        if ($params && !is_array($params)) {
            throw new InvalidParameterException(Constants::ARRAY_REQUIRED_ERROR);
        }
    }

    /**
     * Internal retrieve method.
     *
     * @param string $class
     * @param string $id
     * @param bool $beta
     * @return mixed
     */
    protected function retrieveResource($class, $id, $beta = false)
    {
        $url = $this->instanceUrl($class, $id);

        $response = Requestor::request($this->client, 'get', $url, null, $beta);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Internal retrieve all method.
     *
     * @param string $class
     * @param mixed $params
     * @param bool $beta
     * @return mixed
     */
    protected function allResources($class, $params = null, $beta = false)
    {
        self::validate($params);
        $url = self::classUrl($class);
        $response = Requestor::request($this->client, 'get', $url, $params, $beta);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Internal retrieve next page method.
     * TODO: Use this method in EndShipper and Batch once the API fully support it.
     *
     * @param string $class
     * @param mixed $collection
     * @param int $pageSize
     * @param mixed $optionalParams
     * @return mixed
     */
    protected function getNextPageResources($class, $collection, $pageSize = null, $optionalParams = null)
    {
        $objectName = substr(self::classUrl($class), 1);
        $collectionArray = $collection[$objectName];

        if (empty($collectionArray) || !$collection['has_more']) {
            throw new EndOfPaginationException();
        }

        $params = [
            'page_size' => $pageSize,
            'before_id' => $collectionArray[count($collectionArray) - 1]['id']
        ];

        if (isset($optionalParams)) {
            $params = array_merge($params, $optionalParams);
        }

        $response = $this->allResources($class, $params);

        if (empty($response[$objectName]) || !$response['has_more']) {
            throw new EndOfPaginationException();
        }

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Internal create method.
     *
     * @param string $class
     * @param mixed $params
     * @param bool $beta
     * @return mixed
     */
    protected function createResource($class, $params = null, $beta = false)
    {
        self::validate($params);
        $url = self::classUrl($class);
        $response = Requestor::request($this->client, 'post', $url, $params, $beta);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Internal delete method.
     *
     * @param string $class
     * @param string $id
     * @param mixed $params
     * @param bool $beta
     * @return void
     */
    protected function deleteResource($class, $id, $params = null, $beta = false)
    {
        self::validate();
        $url = $this->instanceUrl($class, $id);

        Requestor::request($this->client, 'delete', $url, $params, $beta);
    }

    /**
     * Internal update method.
     *
     * @param string $class
     * @param string $id
     * @param mixed $params
     * @param string $method
     * @param bool $beta
     * @return mixed
     */
    protected function updateResource($class, $id, $params = null, $method = 'patch', $beta = false)
    {
        self::validate();
        $url = $this->instanceUrl($class, $id);
        $response = Requestor::request($this->client, $method, $url, $params, $beta);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }
}
