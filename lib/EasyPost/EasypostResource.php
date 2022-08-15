<?php

namespace EasyPost;

abstract class EasypostResource extends EasyPostObject
{
    /**
     * The class name of an object.
     *
     * @param string $class
     * @return string
     */
    public static function className($class)
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
    public static function classUrl($class)
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
     * @return string
     * @throws \EasyPost\Error
     */
    public function instanceUrl()
    {
        $id = $this['id'];
        $class = get_class($this);
        if (!$id) {
            throw new Error("Could not determine which URL to request: {$class} instance has invalid ID: {$id}");
        }
        $id = Requestor::utf8($id);
        $classUrl = self::classUrl($class);

        return "{$classUrl}/" . urlencode($id);
    }

    /**
     * Refresh the object from the API.
     *
     * @param bool $beta Whether to use the beta endpoint (optional, default false)
     * @return $this
     * @throws \EasyPost\Error
     */
    public function refresh(bool $beta = false)
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl();

        list($response, $apiKey) = $requestor->request('get', $url, $this->_retrieveOptions, true, $beta);
        $this->refreshFrom($response, $apiKey);

        return $this;
    }

    /**
     * Validate library usage.
     *
     * @param array $params
     * @param string $apiKey
     * @throws \EasyPost\Error
     */
    protected static function validate($params = null, $apiKey = null)
    {
        if ($params && !is_array($params)) {
            throw new Error('You must pass an array as the first argument to EasyPost API method calls.');
        }
        if ($apiKey && !is_string($apiKey)) {
            throw new Error('The second argument to EasyPost API method calls is an optional per-request apiKey, which must be a string.');
        }
    }

    /**
     * Internal retrieve method.
     *
     * @param string $class
     * @param mixed $id
     * @param string $apiKey
     * @return mixed
     */
    protected static function retrieveResource($class, $id, $apiKey = null, $beta = false)
    {
        if ($id instanceof EasypostResource) {
            $id = $id->id;
        }
        $instance = new $class($id, $apiKey);
        $instance->refresh($beta);

        return $instance;
    }

    /**
     * Internal retrieve all method.
     *
     * @param string $class
     * @param mixed $params
     * @param string $apiKey
     * @return mixed
     * @throws \EasyPost\Error
     */
    protected static function allResources($class, $params = null, $apiKey = null, $beta = false)
    {
        self::validate($params, $apiKey);
        $requestor = new Requestor($apiKey);
        $url = self::classUrl($class);
        list($response, $apiKey) = $requestor->request('get', $url, $params, true, $beta);

        return Util::convertToEasyPostObject($response, $apiKey);
    }

    /**
     * Internal create method.
     *
     * @param string $class
     * @param mixed $params
     * @param string $apiKey
     * @param string $urlModifier
     * @return mixed
     * @throws \EasyPost\Error
     */
    protected static function createResource($class, $params = null, $apiKey = null, $urlModifier = null, $beta = false)
    {
        self::validate($params, $apiKey);
        $requestor = new Requestor($apiKey);
        $url = self::classUrl($class);
        if ($urlModifier != null) {
            $url .= $urlModifier;
        }

        list($response, $apiKey) = $requestor->request('post', $url, $params, true, $beta);

        return Util::convertToEasyPostObject($response, $apiKey);
    }

    /**
     * Internal update (save) method.
     *
     * @param string $class
     * @return $this
     * @throws \EasyPost\Error
     */
    protected function saveResource($class, $beta = false, $method = 'patch')
    {
        self::validate();
        if (count($this->_unsavedValues)) {
            $requestor = new Requestor($this->_apiKey);
            $url = $this->instanceUrl();
            $params = [self::className($class) => $this->_unsavedValues];
            list($response, $apiKey) = $requestor->request($method, $url, $params, true, $beta);
            $this->refreshFrom($response, $apiKey);
        }

        return $this;
    }

    /**
     * Internal delete method.
     *
     * @param mixed $params
     * @return $this
     * @throws \EasyPost\Error
     */
    protected function deleteResource($params = null, $noRefresh = null)
    {
        self::validate();
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl();
        list($response, $apiKey) = $requestor->request('delete', $url, $params);
        if (!$noRefresh) {
            $this->refreshFrom($response, $apiKey);
        }

        return $this;
    }

    /**
     * Internal update method.
     *
     * @param mixed $params
     * @return $this
     * @throws \EasyPost\Error
     */
    protected function updateResource($params = null)
    {
        self::validate();
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl();
        list($response, $apiKey) = $requestor->request('patch', $url, $params);
        $this->refreshFrom($response, $apiKey);

        return $this;
    }
}
