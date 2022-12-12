<?php

namespace EasyPost\Service;

use EasyPost\Exception\Error;
use EasyPost\Http\Requestor;
use EasyPost\Util\InternalUtil;

/**
 * Report service containing all the logic to make API calls.
 */
class ReportService extends BaseService
{
    private static $modelClass = 'Report';

    /**
     * Retrieve a report.
     *
     * @param string $id
     * @return mixed
     */
    public function retrieve($id)
    {
        return self::retrieveResource(self::$modelClass, $id);
    }

    /**
     * Retrieve all reports.
     *
     * @param mixed $params
     * @return mixed
     * @throws Error
     */
    public function all($params = null)
    {
        if (!isset($params) || !isset($params['type'])) {
            throw new Error('Undetermined Report Type');
        } else {
            $type = $params['type'];

            self::validate($params);
            $requestor = new Requestor($this->client);

            $url = self::reportUrl($type);

            $response = $requestor->request('get', $url, $params);

            return InternalUtil::convertToEasyPostObject($this->client, $response);
        }
    }

    /**
     * Create a report.
     *
     * @param mixed $params
     * @return mixed
     */
    public function create($params = null)
    {
        if (!isset($params['type'])) {
            throw new Error('Undetermined Report Type');
        }

        $url = self::reportUrl($params['type']);

        self::validate($params);
        $requestor = new Requestor($this->client);

        $response = $requestor->request('post', $url, $params);

        return InternalUtil::convertToEasyPostObject($this->client, $response);
    }

    /**
     * Generate report url format.
     *
     * @param string $type
     * @return mixed
     */
    private function reportUrl($type)
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
