<?php

namespace EasyPost;

/**
 * @package EasyPost
 * @property string $id
 * @property string $object
 * @property string $mode
 * @property string $status
 * @property string $start_date
 * @property string $end_date
 * @property bool $include_children
 * @property string $url
 * @property string $url_expires_at
 * @property bool $send_email
 * @property string $created_at
 * @property string $updated_at
 */
class Report extends EasypostResource
{
    /**
     * Retrieve a report.
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
     * Retrieve all reports.
     *
     * @param mixed $params
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

            self::_validate($params, $apiKey);
            $requestor = new Requestor($apiKey);

            $url = self::reportUrl($type);

            list($response, $apiKey) = $requestor->request('get', $url, $params);

            return Util::convertToEasyPostObject($response, $apiKey);
        }
    }

    /**
     * Create a report.
     *
     * @param mixed $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create($params = null, $apiKey = null)
    {
        if ((isset($params['columns']) && is_array($params['columns'])) || (isset($params['additional_columns']) && is_array($params['additional_columns']))) {
            $columns = "";
            if (isset($params['columns'])) {
                $columns = $params['columns'];
                unset($params['columns']);
            }

            $additional_columns = "";
            if (isset($params['additional_columns'])) {
                $additional_columns = $params['additional_columns'];
                unset($params['additional_columns']);
            }

            $urlMod = "?";

            if (is_array($columns)) {
                foreach ($columns as $column) {
                    $urlMod .= "columns[]=" . $column . "&";
                }
            }

            if (is_array($additional_columns)) {
                foreach ($additional_columns as $additional_column) {
                    $urlMod .= "additional_columns[]=" . $additional_column . "&";
                }
            }
        }

        if (!isset($params['type'])) {
            throw new Error('Undetermined Report Type');
        } else {
            $type = $params['type'];

            self::_validate($params, $apiKey);
            $requestor = new Requestor($apiKey);

            $url = self::reportUrl($type);

            list($response, $apiKey) = $requestor->request('post', $url, $params, true);
            return Util::convertToEasyPostObject($response, $apiKey);
        }
    }

    /**
     * Generate report url format.
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
