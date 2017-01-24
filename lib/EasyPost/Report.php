<?php

namespace EasyPost;

class Report extends EasypostResource
{
    // Define a psuedo-constant
    private static $report_prefixes = array(
        'shprep' => 'shipment',
        'plrep'  => 'payment_log',
        'trkrep' => 'tracker'
    );
    public static function getReportPrefixes($index = false){
        return $index !== false ? self::$report_prefixes[$index] : self::$report_prefixes;
    }

    /**
     * rewrite instanceUrl for reports to handle different format
     *
     * @return string
     * @throws \EasyPost\Error
     */
    public function instanceUrl()
    {
        $id = $this['id'];
        if (!$id) {
            throw new Error('Could not determine which URL to request: {$class} instance has invalid ID: {$id}');
        }
        $id = Requestor::utf8($id);
        $id_parts = explode('_', $id);
        $id_prefix = $id_parts[0];

        $type = self::getReportPrefixes($id_prefix);

        return self::reportUrl($type) . urlencode($id);
    }

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
        if ($id instanceof EasypostResource) {
            $id = $id->id;
        }

        $id_parts = explode('_', $id);
        $id_prefix = $id_parts[0];
        $report_type = self::getReportPrefixes($id_prefix);

        if (!isset($report_type)) {
            throw new Error('Undetermined Report Type');
        } else {
            return self::_retrieve(get_class(), $id, $apiKey);
        }
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
        if (!isset($params) || !isset($params['type']) || !in_array($params['type'], array_values(self::getReportPrefixes()))) {
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

        if (!isset($params['report']['type']) || !in_array($params['report']['type'], array_values(self::getReportPrefixes()))) {
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

