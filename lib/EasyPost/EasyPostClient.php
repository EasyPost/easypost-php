<?php

namespace EasyPost;

use EasyPost\Constant\Constants;
use EasyPost\Exception\General\EasyPostException;
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
use EasyPost\Service\ClaimService;
use EasyPost\Service\CustomsInfoService;
use EasyPost\Service\CustomsItemService;
use EasyPost\Service\EndShipperService;
use EasyPost\Service\EventService;
use EasyPost\Service\InsuranceService;
use EasyPost\Service\LumaService;
use EasyPost\Service\OrderService;
use EasyPost\Service\ParcelService;
use EasyPost\Service\PickupService;
use EasyPost\Service\RateService;
use EasyPost\Service\ReferralCustomerService;
use EasyPost\Service\RefundService;
use EasyPost\Service\ReportService;
use EasyPost\Service\ScanFormService;
use EasyPost\Service\ShipmentService;
use EasyPost\Service\SmartRateService;
use EasyPost\Service\TrackerService;
use EasyPost\Service\UserService;
use EasyPost\Service\WebhookService;
use GuzzleHttp\Client;

/**
 * Client used to send requests to the EasyPost API.
 *
 * @package EasyPost
 * @property AddressService $address
 * @property ApiKeyService $apiKeys
 * @property BatchService $batch
 * @property BetaRateService $betaRate
 * @property BetaReferralCustomerService $betaReferralCustomer
 * @property BillingService $billing
 * @property CarrierAccountService $carrierAccount
 * @property CarrierMetadataService $carrierMetadata
 * @property ClaimService $claim
 * @property CustomsInfoService $customsInfo
 * @property CustomsItemService $customsItem
 * @property EndShipperService $endShipper
 * @property EventService $event
 * @property InsuranceService $insurance
 * @property LumaService $luma
 * @property OrderService $order
 * @property ParcelService $parcel
 * @property PickupService $pickup
 * @property RateService $rate
 * @property ReferralCustomerService $referralCustomer
 * @property RefundService $refund
 * @property ReportService $report
 * @property ScanFormService $scanForm
 * @property ShipmentService $shipment
 * @property SmartRateService $smartRate
 * @property TrackerService $tracker
 * @property UserService $user
 * @property WebhookService $webhook
 */
class EasyPostClient extends BaseService
{
    // Client properties
    private string $apiKey;
    private float $timeout;
    private string $apiBase;
    private ?object $mockingUtility;
    public RequestHook $requestEvent;
    public ResponseHook $responseEvent;
    public Client $httpClient;

    /**
     * Constructor for an EasyPostClient.
     *
     * @param string $apiKey
     * @param float $timeout
     * @param string $apiBase
     * @param object|null $mockingUtility
     */
    public function __construct(
        string $apiKey,
        float $timeout = Constants::TIMEOUT,
        string $apiBase = Constants::API_BASE,
        ?object $mockingUtility = null
    ) {
        // Client properties
        $this->apiKey = $apiKey;
        $this->timeout = $timeout;
        $this->apiBase = $apiBase;
        $this->mockingUtility = $mockingUtility;
        $this->requestEvent = new RequestHook();
        $this->responseEvent = new ResponseHook();
        $this->httpClient = new Client();
    }

    /**
     * Get a Service when calling a property of an EasyPostClient.
     *
     * @param string $serviceName
     * @return mixed
     * @throws EasyPostException
     */
    public function __get(string $serviceName)
    {
        $serviceClassMap = [
            'address' => AddressService::class,
            'apiKeys' => ApiKeyService::class,
            'batch' => BatchService::class,
            'betaRate' => BetaRateService::class,
            'betaReferralCustomer' => BetaReferralCustomerService::class,
            'billing' => BillingService::class,
            'carrierAccount' => CarrierAccountService::class,
            'carrierMetadata' => CarrierMetadataService::class,
            'claim' => ClaimService::class,
            'customsInfo' => CustomsInfoService::class,
            'customsItem' => CustomsItemService::class,
            'endShipper' => EndShipperService::class,
            'event' => EventService::class,
            'insurance' => InsuranceService::class,
            'luma' => LumaService::class,
            'order' => OrderService::class,
            'parcel' => ParcelService::class,
            'pickup' => PickupService::class,
            'rate' => RateService::class,
            'referralCustomer' => ReferralCustomerService::class,
            'refund' => RefundService::class,
            'report' => ReportService::class,
            'scanForm' => ScanFormService::class,
            'shipment' => ShipmentService::class,
            'smartRate' => SmartRateService::class,
            'tracker' => TrackerService::class,
            'user' => UserService::class,
            'webhook' => WebhookService::class,
        ];

        if (array_key_exists($serviceName, $serviceClassMap)) {
            return new $serviceClassMap[$serviceName]($this);
        } else {
            // TODO: checking for `_parent` is a hack and should be fixed when we revisit the
            // (de)serialization of objects in this lib.
            if ($serviceName != '_parent') {
                throw new EasyPostException(
                    sprintf(Constants::UNDEFINED_PROPERTY_ERROR, 'EasyPostClient', $serviceName)
                );
            }
        }
    }

    /**
     * Get the API key of an EasyPostClient.
     *
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * Get the timeout of an EasyPostClient.
     *
     * @return float
     */
    public function getTimeout(): float
    {
        return $this->timeout;
    }

    /**
     * Get the API Base URL of an EasyPostClient.
     *
     * @return string
     */
    public function getApiBase(): string
    {
        return $this->apiBase;
    }

    /**
     * Get whether this client is configured to mock requests
     *
     * @return bool
     */
    public function mock(): bool
    {
        return $this->mockingUtility !== null;
    }

    /**
     * Get the mock requests of an EasyPostClient.
     *
     * @return object|null
     */
    public function getMockingUtility(): ?object
    {
        return $this->mockingUtility;
    }

    /**
     * Subscribe functions to run when a request event occurs.
     *
     * @param callable $function
     * @return void
     */
    public function subscribeToRequestHook(callable $function): void
    {
        $this->requestEvent->addHandler($function);
    }

    /**
     * Unsubscribe functions from running when a request even occurs.
     *
     * @param callable $function
     * @return void
     */
    public function unsubscribeFromRequestHook(callable $function): void
    {
        $this->requestEvent->removeHandler($function);
    }

    /**
     * Subscribe functions to run when a response event occurs.
     *
     * @param callable $function
     * @return void
     */
    public function subscribeToResponseHook(callable $function): void
    {
        $this->responseEvent->addHandler($function);
    }

    /**
     * Unsubscribe functions from running when a response even occurs.
     *
     * @param callable $function
     * @return void
     */
    public function unsubscribeFromResponseHook(callable $function): void
    {
        $this->responseEvent->removeHandler($function);
    }
}
