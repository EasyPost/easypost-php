<?php

namespace EasyPost;

class Report extends EasypostResource
{
    /**
     * retrieve a report
     *
     * @param string $id
     * @param string $apiKey
     * @return mixed
     * @throws \EasyPost\Error
     */
    public static function retrieve($id, $apiKey = null)
    {
        return self::_retrieve(get_class(), $id, $apiKey);
    }


    /**
     * retrieve all reports
     *
     * @param mixed  $params
     * @param string $apiKey
     * @return mixed
     * @throws \EasyPost\Error
     */
    public static function all($params = null, $apiKey = null)
    {
        if (!isset($params) || !isset($params['type'])) {
            throw new Error('Undetermined Report Type');
        } else {
            $type = $params['type'];

            self::_validate('all', $params, $apiKey);
            $requestor = new Requestor($apiKey);

            $url = self::reportUrl($type);

            list($response, $apiKey) = $requestor->request('get', $url, $params);

            return Util::convertToEasyPostObject($response, $apiKey);
        }
    }

    /**
     * create a report
     *
     * @param mixed  $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create($params = null, $apiKey = null)
    {
        if (!isset($params['report']) || !is_array($params['report'])) {
            $clone = $params;
            unset($params);
            $params['report'] = $clone;
        }

        if (!isset($params['report']['type'])) {
            throw new Error('Undetermined Report Type');
        } else {
            $type = $params['report']['type'];

            self::_validate('create', $params, $apiKey);
            $requestor = new Requestor($apiKey);

            $url = self::reportUrl($type);

            list($response, $apiKey) = $requestor->request('post', $url, $params, true);
            return Util::convertToEasyPostObject($response, $apiKey);
        }
    }

    /**
     * generate report url format
     *
     * @param string $type
     * @return mixed
     * @throws \EasyPost\Error
     */
    protected static function reportUrl($type)
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

        return "/reports/{$name}/";
    }
}

