<?php

namespace EasyPost;

use EasyPost\Constant\Constants;
use EasyPost\Exception\General\EasyPostException;
use EasyPost\Exception\General\MissingParameterException;
use EasyPost\Hook\RequestHook;
use EasyPost\Hook\ResponseHook;
use EasyPost\Service\AddressService;
use EasyPost\Service\ApiKeyService;
use EasyPost\Service\BaseService;
use EasyPost\Service\BatchService;
use EasyPost\Service\BetaRateService;
use EasyPost\Service\BetaReferralCustomerService;
use EasyPost\Service\BillingService;
use EasyPost\Service\CarrierAccountService;
use EasyPost\Service\CarrierMetadataService;
use EasyPost\Service\CustomsInfoService;
use EasyPost\Service\CustomsItemService;
use EasyPost\Service\EndShipperService;
use EasyPost\Service\EventService;
use EasyPost\Service\InsuranceService;
use EasyPost\Service\OrderService;
use EasyPost\Service\ParcelService;
use EasyPost\Service\PickupService;
use EasyPost\Service\RateService;
use EasyPost\Service\ReferralCustomerService;
use EasyPost\Service\RefundService;
use EasyPost\Service\ReportService;
use EasyPost\Service\ScanFormService;
use EasyPost\Service\ShipmentService;
use EasyPost\Service\TrackerService;
use EasyPost\Service\UserService;
use EasyPost\Service\WebhookService;

/**
 * Client used to send requests to the EasyPost API.
 *
 * @package EasyPost
 * @property AddressService $address
 * @property ApiKeyService $apiKey
 * @property BatchService $batch
 * @property BetaReferralCustomer $betaReferralCustomer
 * @property BillingService $billing
 * @property CarrierAccountService $carrierAccount
 * @property CustomsInfoService $customsInfo
 * @property CustomsItemService $customsItem
 * @property EndShipperService $endShipper
 * @property EventService $event
 * @property InsuranceService $insurance
 * @property OrderService $order
 * @property ParcelService $parcel
 * @property PickupService $pickup
 * @property RateService $rate
 * @property ReferralCustomerService $referralCustomer
 * @property RefundService $refund
 * @property ReportService $report
 * @property ScanFormService $scanForm
 * @property ShipmentService $shipment
 * @property TrackerService $tracker
 * @property UserService $user
 * @property WebhookService $webhook
 */
class EasyPostClient extends BaseService
{
    // Client properties
    private $apiKey;
    private $timeout;
    private $apiBase;
    private $mockingUtility;
    public $requestEvent;
    public $responseEvent;

    /**
     * Constructor for an EasyPostClient.
     *
     * @param string $apiKey
     * @param float $timeout
     * @param string $apiBase
     * @param object $mockingUtility
     */
    public function __construct(
        $apiKey,
        $timeout = Constants::TIMEOUT,
        $apiBase = Constants::API_BASE,
        $mockingUtility = null
    ) {
        // Client properties
        $this->apiKey = $apiKey;
        $this->timeout = $timeout;
        $this->apiBase = $apiBase;
        $this->mockingUtility = $mockingUtility;
        $this->requestEvent = new RequestHook();
        $this->responseEvent = new ResponseHook();

        if (!$this->apiKey) {
            throw new MissingParameterException(
                'No API key provided. See https://www.easypost.com/docs for details, or contact ' . Constants::SUPPORT_EMAIL . ' for assistance.' // phpcs:ignore
            );
        }
    }

    /**
     * Get a Service when calling a property of an EasyPostClient.
     *
     * @param string $serviceName
     * @return BaseService
     * @throws EasyPostException
     */
    public function __get($serviceName)
    {
        $serviceClassMap = [
            'address' => AddressService::class,
            'apiKeys' => ApiKeyService::class,
            'batch' => BatchService::class,
            'betaReferralCustomer' => BetaReferralCustomerService::class,
            'betaRate' => BetaRateService::class,
            'billing' => BillingService::class,
            'carrierAccount' => CarrierAccountService::class,
            'carrierMetadata' => CarrierMetadataService::class,
            'customsInfo' => CustomsInfoService::class,
            'customsItem' => CustomsItemService::class,
            'endShipper' => EndShipperService::class,
            'event' => EventService::class,
            'insurance' => InsuranceService::class,
            'order' => OrderService::class,
            'parcel' => ParcelService::class,
            'pickup' => PickupService::class,
            'rate' => RateService::class,
            'referralCustomer' => ReferralCustomerService::class,
            'refund' => RefundService::class,
            'report' => ReportService::class,
            'scanForm' => ScanFormService::class,
            'shipment' => ShipmentService::class,
            'tracker' => TrackerService::class,
            'user' => UserService::class,
            'webhook' => WebhookService::class,
        ];

        if (array_key_exists($serviceName, $serviceClassMap)) {
            return new $serviceClassMap[$serviceName]($this);
        } else {
            throw new EasyPostException(
                sprintf(Constants::UNDEFINED_PROPERTY_ERROR, 'EasyPostClient', $serviceName)
            );
        }
    }

    /**
     * Get the API key of an EasyPostClient.
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Get the timeout of an EasyPostClient.
     *
     * @return float
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Get the API Base URL of an EasyPostClient.
     *
     * @return string
     */
    public function getApiBase()
    {
        return $this->apiBase;
    }

    /**
     * Get whether this client is configured to mock requests
     *
     * @return bool
     */
    public function mock()
    {
        return $this->mockingUtility !== null;
    }

    /**
     * Get the mock requests of an EasyPostClient.
     *
     * @return object
     */
    public function getMockingUtility()
    {
        return $this->mockingUtility;
    }

    /**
     * Subscribe functions to run when a request event occurs.
     *
     * @param callable $function
     * @return void
     */
    public function subscribeToRequestHook($function)
    {
        $this->requestEvent->addHandler($function);
    }

    /**
     * Unsubscribe functions from running when a request even occurs.
     *
     * @param callable $function
     * @return void
     */
    public function unsubscribeFromRequestHook($function)
    {
        $this->requestEvent->removeHandler($function);
    }

    /**
     * Subscribe functions to run when a response event occurs.
     *
     * @param callable $function
     * @return void
     */
    public function subscribeToResponseHook($function)
    {
        $this->responseEvent->addHandler($function);
    }

    /**
     * Unsubscribe functions from running when a response even occurs.
     *
     * @param callable $function
     * @return void
     */
    public function unsubscribeFromResponseHook($function)
    {
        $this->responseEvent->removeHandler($function);
    }
}
