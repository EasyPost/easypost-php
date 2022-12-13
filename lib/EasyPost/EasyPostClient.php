<?php

namespace EasyPost;

use EasyPost\Constant\Constants;
use EasyPost\Exception\Error;
use EasyPost\Service\AddressService;
use EasyPost\Service\BaseService;
use EasyPost\Service\BatchService;
use EasyPost\Service\BillingService;
use EasyPost\Service\CarrierAccountService;
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
 * @property BatchService $batch;
 * @property BillingService $billing;
 * @property CarrierAccountService $carrierAccount;
 * @property CustomsInfoService $customsInfo;
 * @property CustomsItemService $customsItem;
 * @property EndShipperService $endShipper;
 * @property EventService $event;
 * @property InsuranceService $insurance;
 * @property OrderService $order;
 * @property ParcelService $parcel;
 * @property PickupService $pickup;
 * @property RateService $rate;
 * @property ReferralCustomerService $referralCustomer;
 * @property RefundService $refund;
 * @property ReportService $report;
 * @property ScanFormService $scanForm;
 * @property ShipmentService $shipment;
 * @property TrackerService $tracker;
 * @property UserService $user;
 * @property WebhookService $webhook;
 */
class EasyPostClient extends BaseService
{
    // Client properties
    private $apiKey;
    private $timeout;
    private $apiBase;

    // Services
    public $address;
    public $batch;
    public $billing;
    public $carrierAccount;
    public $customsInfo;
    public $customsItem;
    public $endShipper;
    public $event;
    public $insurance;
    public $order;
    public $parcel;
    public $pickup;
    public $rate;
    public $referralCustomer;
    public $refund;
    public $report;
    public $scanForm;
    public $shipment;
    public $tracker;
    public $user;
    public $webhook;

    /**
     * Constructor for an EasyPostClient.
     *
     * @param string $apiKey
     * @param float $timeout
     * @param string $apiBase
     */
    public function __construct($apiKey, $timeout = Constants::TIMEOUT, $apiBase = Constants::API_BASE)
    {
        // Client properties
        $this->apiKey = $apiKey;
        $this->timeout = $timeout;
        $this->apiBase = $apiBase;

        // Services
        // TODO: Make these all read only when we support PHP >= 8.1
        $this->address = new AddressService($this);
        $this->batch = new BatchService($this);
        $this->billing = new BillingService($this);
        $this->carrierAccount = new CarrierAccountService($this);
        $this->customsInfo = new CustomsInfoService($this);
        $this->customsItem = new CustomsItemService($this);
        $this->endShipper = new EndShipperService($this);
        $this->event = new EventService($this);
        $this->insurance = new InsuranceService($this);
        $this->order = new OrderService($this);
        $this->parcel = new ParcelService($this);
        $this->pickup = new PickupService($this);
        $this->rate = new RateService($this);
        $this->referralCustomer = new ReferralCustomerService($this);
        $this->refund = new RefundService($this);
        $this->report = new ReportService($this);
        $this->scanForm = new ScanFormService($this);
        $this->shipment = new ShipmentService($this);
        $this->tracker = new TrackerService($this);
        $this->user = new UserService($this);
        $this->webhook = new WebhookService($this);

        if (!$this->apiKey) {
            throw new Error('No API key provided. See https://www.easypost.com/docs for details, or contact ' . Constants::SUPPORT_EMAIL . ' for assistance.');
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
}
