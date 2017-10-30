<?php

namespace EasyPost;

abstract class EasypostResource extends EasyPostObject
{
    /**
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
        $class = substr($class, 0, 1) . preg_replace("/([A-Z])/", "_$1", substr($class, 1)); // Camel -> snake
        $name = urlencode($class);
        $name = strtolower($name);

        return $name;
    }

    /**
     * @param string $class
     * @return string
     */
    public static function classUrl($class)
    {
        $className = self::className($class);
        if (substr($className, -1) !== "s" && substr($className, -1) !== "h") {
            return "/{$className}s";
        }

        return "/{$className}es";
    }

    /**
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
     * @return $this
     * @throws \EasyPost\Error
     */
    public function refresh()
    {
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl();

        list($response, $apiKey) = $requestor->request('get', $url, $this->_retrieveOptions);
        $this->refreshFrom($response, $apiKey);

        return $this;
    }

    /**
     * @param string $method
     * @param array  $params
     * @param string $apiKey
     * @throws \EasyPost\Error
     */
    protected static function _validate($method, $params = null, $apiKey = null)
    {
        if ($params && !is_array($params)) {
            throw new Error("You must pass an array as the first argument to EasyPost API method calls.");
        }
        if ($apiKey && !is_string($apiKey)) {
            throw new Error('The second argument to EasyPost API method calls is an optional per-request apiKey, which must be a string.');
        }
    }

    /**
     * @param string $class
     * @param mixed  $id
     * @param string $apiKey
     * @return mixed
     */
    protected static function _retrieve($class, $id, $apiKey = null)
    {
        if ($id instanceof EasypostResource) {
            $id = $id->id;
        }
        $instance = new $class($id, $apiKey);
        $instance->refresh();

        return $instance;
    }

    /**
     * @param string $class
     * @param mixed  $params
     * @param string $apiKey
     * @return mixed
     * @throws \EasyPost\Error
     */
    protected static function _all($class, $params = null, $apiKey = null)
    {
        self::_validate('all', $params, $apiKey);
        $requestor = new Requestor($apiKey);
        $url = self::classUrl($class);
        list($response, $apiKey) = $requestor->request('get', $url, $params);

        return Util::convertToEasyPostObject($response, $apiKey);
    }

    /**
     * @param string $class
     * @param mixed  $params
     * @param string $apiKey
     * @param string $urlModifier
     * @return mixed
     * @throws \EasyPost\Error
     */
    protected static function _create($class, $params = null, $apiKey = null, $urlModifier = null, $apiKeyRequired = true)
    {
        self::_validate('create', $params, $apiKey);
        $requestor = new Requestor($apiKey);
        $url = self::classUrl($class);
        if ($urlModifier != null) {
            $url .= $urlModifier;
        }

        list($response, $apiKey) = $requestor->request('post', $url, $params, $apiKeyRequired);

        return Util::convertToEasyPostObject($response, $apiKey);
    }

    /**
     * @param string $class
     * @return $this
     * @throws \EasyPost\Error
     */
    protected function _save($class)
    {
        self::_validate('save');
        if (count($this->_unsavedValues)) {
            $requestor = new Requestor($this->_apiKey);
            $url = $this->instanceUrl();
            $params = array(self::className($class) => $this->_unsavedValues);
            list($response, $apiKey) = $requestor->request('patch', $url, $params);
            $this->refreshFrom($response, $apiKey);
        }

        return $this;
    }

    /**
     * @param string $class
     * @param mixed  $params
     * @return $this
     * @throws \EasyPost\Error
     */
    protected function _delete($class, $params = null, $no_refresh = null)
    {
        self::_validate('delete');
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl();
        list($response, $apiKey) = $requestor->request('delete', $url, $params);
        if (!$no_refresh){
            $this->refreshFrom($response, $apiKey);
        }

        return $this;
    }

    /**
     * @param string $class
     * @param mixed  $params
     * @return $this
     * @throws \EasyPost\Error
     */
    protected function _update($class, $params = null)
    {
        self::_validate('put');
        $requestor = new Requestor($this->_apiKey);
        $url = $this->instanceUrl();
        list($response, $apiKey) = $requestor->request('put', $url, $params);
        $this->refreshFrom($response, $apiKey);

        return $this;
    }
}
