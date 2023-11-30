<?php

namespace EasyPost\Service;

/**
 * Webhook service containing all the logic to make API calls.
 */
class WebhookService extends BaseService
{
    /**
     * Retrieve a webhook.
     *
     * @param string $id
     * @return mixed
     */
    public function retrieve(string $id): mixed
    {
        return self::retrieveResource(self::serviceModelClassName(self::class), $id);
    }

    /**
     * Retrieve all webhooks.
     *
     * @param mixed $params
     * @return mixed
     */
    public function all(mixed $params = null): mixed
    {
        return self::allResources(self::serviceModelClassName(self::class), $params);
    }

    /**
     * Delete a webhook.
     *
     * @param string $id
     * @param mixed $params
     * @return void
     */
    public function delete(string $id, mixed $params = null): void
    {
        self::deleteResource(self::serviceModelClassName(self::class), $id, $params);
    }

    /**
     * Update a webhook.
     *
     * @param string $id
     * @param mixed $params
     * @return mixed
     */
    public function update(string $id, mixed $params = null): mixed
    {
        if (!isset($params['webhook']) || !is_array($params['webhook'])) {
            $clone = $params;
            unset($params);
            $params['webhook'] = $clone;
        }

        return self::updateResource(self::serviceModelClassName(self::class), $id, $params);
    }

    /**
     * Create a webhook.
     *
     * @param mixed $params
     * @return mixed
     */
    public function create(mixed $params = null): mixed
    {
        if (!isset($params['webhook']) || !is_array($params['webhook'])) {
            $clone = $params;
            unset($params);
            $params['webhook'] = $clone;
        }

        return self::createResource(self::serviceModelClassName(self::class), $params);
    }
}
