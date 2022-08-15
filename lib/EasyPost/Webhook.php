<?php

namespace EasyPost;

use Normalizer;

/**
 * @package EasyPost
 * @property string $id
 * @property string $object
 * @property string $modoe
 * @property string $url
 * @property string $disabled_at
 */
class Webhook extends EasypostResource
{
    /**
     * Retrieve a webhook.
     *
     * @param string $id
     * @param string $apiKey
     * @return mixed
     */
    public static function retrieve($id, $apiKey = null)
    {
        return self::retrieveResource(get_class(), $id, $apiKey);
    }

    /**
     * Retrieve all webhooks.
     *
     * @param mixed $params
     * @param string $apiKey
     * @return mixed
     */
    public static function all($params = null, $apiKey = null)
    {
        return self::allResources(get_class(), $params, $apiKey);
    }

    /**
     * Delete a webhook.
     *
     * @param string $apiKey
     * @return $this
     */
    public function delete($params = null, $apiKey = null)
    {
        return $this->deleteResource($params, true);
    }

    /**
     * Update a webhook.
     *
     * @param mixed $params
     * @param string $apiKey
     * @return $this
     */
    public function update($params = null, $apiKey = null)
    {
        if (!isset($params['webhook']) || !is_array($params['webhook'])) {
            $clone = $params;
            unset($params);
            $params['webhook'] = $clone;
        }

        return $this->updateResource($params);
    }

    /**
     * Create a webhook.
     *
     * @param mixed $params
     * @param string $apiKey
     * @return mixed
     */
    public static function create($params = null, $apiKey = null)
    {
        if (!isset($params['webhook']) || !is_array($params['webhook'])) {
            $clone = $params;
            unset($params);
            $params['webhook'] = $clone;
        }

        return self::createResource(get_class(), $params, $apiKey);
    }

    /**
     * Validate a webhook originated from EasyPost by comparing the HMAC header to a shared secret.
     * If the signatures do not match, an error will be raised signifying the webhook either did not originate
     * from EasyPost or the secrets do not match. If the signatures do match, the `event_body` will be returned as JSON.
     *
     * @param mixed $eventBody
     * @param mixed $headers
     * @param string $webhookSecret
     * @return mixed
     */
    public static function validateWebhook($eventBody, $headers, $webhookSecret)
    {
        $easypostHmacSignature = $headers['X-Hmac-Signature'] ?? null;

        if ($easypostHmacSignature != null) {
            $normalizedSecret = Normalizer::normalize($webhookSecret, Normalizer::FORM_KD);
            $encodedSecret = mb_convert_encoding($normalizedSecret, 'UTF-8');

            $expectedSignature = hash_hmac('sha256', $eventBody, $encodedSecret);
            $digest = "hmac-sha256-hex=$expectedSignature";

            if (hash_equals($digest, $easypostHmacSignature)) {
                $webhookBody = json_decode($eventBody);
            } else {
                throw new Error('Webhook received did not originate from EasyPost or had a webhook secret mismatch.');
            }
        } else {
            throw new Error('Webhook received does not contain an HMAC signature.');
        }

        return $webhookBody;
    }
}
