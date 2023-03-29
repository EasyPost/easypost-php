<?php

namespace EasyPost\Service;

use EasyPost\Constant\Constants;
use EasyPost\Exception\General\MissingParameterException;
use EasyPost\Exception\General\EndOfPaginationException;
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
    public function retrieve($id)
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
    public function all($params = null)
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
     * @param string $pageSize
     * @return mixed
     */
    public function getNextPage($reports, $pageSize = null)
    {
        $reportArray = $reports['reports'];

        if (empty($reportArray) || !$reports['has_more']) {
            throw new EndOfPaginationException();
        }

        $params = [
            'page_size' => $pageSize,
            'before_id' => $reportArray[count($reportArray) - 1]['id']
        ];
        $url = self::reportUrl($reports->type);
        $response = Requestor::request($this->client, 'get', $url, $params);

        if (empty($response['reports']) || !$response['has_more']) {
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
    public function create($params = null)
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

        return "/reports/{$name}";
    }
}
