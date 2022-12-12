<?php

namespace EasyPost\Service;

/**
 * Webhook service containing all the logic to make API calls.
 */
class WebhookService extends BaseService
{
    private static $modelClass = 'Webhook';

    /**
     * Retrieve a webhook.
     *
     * @param string $id
     * @return mixed
     */
    public function retrieve($id)
    {
        return self::retrieveResource(self::$modelClass, $id);
    }

    /**
     * Retrieve all webhooks.
     *
     * @param mixed $params
     * @return mixed
     */
    public function all($params = null)
    {
        return self::allResources(self::$modelClass, $params);
    }

    /**
     * Delete a webhook.
     *
     * @param string $id
     * @param mixed $params
     * @return void
     */
    public function delete($id, $params = null)
    {
        self::deleteResource(self::$modelClass, $id, $params);
    }

    /**
     * Update a webhook.
     *
     * @param string $id
     * @param mixed $params
     * @return mixed
     */
    public function update($id, $params = null)
    {
        if (!isset($params['webhook']) || !is_array($params['webhook'])) {
            $clone = $params;
            unset($params);
            $params['webhook'] = $clone;
        }

        return self::updateResource(self::$modelClass, $id, $params);
    }

    /**
     * Create a webhook.
     *
     * @param mixed $params
     * @return mixed
     */
    public function create($params = null)
    {
        if (!isset($params['webhook']) || !is_array($params['webhook'])) {
            $clone = $params;
            unset($params);
            $params['webhook'] = $clone;
        }

        return self::createResource(self::$modelClass, $params);
    }
}
