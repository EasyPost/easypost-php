<?php

namespace EasyPost\Service;

use EasyPost\Exception\Error;
use EasyPost\Http\Requestor;
use EasyPost\Util\InternalUtil;

class BaseService
{
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
     * @throws \EasyPost\Exception\Error
     */
    protected function instanceUrl($class, $id)
    {
        if (!$id) {
            throw new Error("Could not determine which URL to request: {$class} instance has invalid ID: {$id}");
        }
        $id = Requestor::utf8($id);
        $classUrl = self::classUrl($class);

        return "{$classUrl}/" . urlencode($id);
    }

    /**
     * Validate library usage.
     *
     * @param array $params
     * @throws \EasyPost\Exception\Error
     */
    protected static function validate($params = null)
    {
        if ($params && !is_array($params)) {
            throw new Error('You must pass an array as the first argument to EasyPost API method calls.');
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
        $requestor = new Requestor($this->client);
        $url = $this->instanceUrl($class, $id);

        $response = $requestor->request('get', $url, null, $beta);

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
        $requestor = new Requestor($this->client);
        $url = self::classUrl($class);
        $response = $requestor->request('get', $url, $params, $beta);

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
        $requestor = new Requestor($this->client);
        $url = self::classUrl($class);

        $response = $requestor->request('post', $url, $params, $beta);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Internal delete method.
     *
     * @param string $class
     * @param string $id
     * @param mixed $params
     * @return void
     */
    protected function deleteResource($class, $id, $params = null)
    {
        self::validate();
        $requestor = new Requestor($this->client);
        $url = $this->instanceUrl($class, $id);
        $requestor->request('delete', $url, $params);
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
        $requestor = new Requestor($this->client);
        $url = $this->instanceUrl($class, $id);
        $response = $requestor->request($method, $url, $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }
}