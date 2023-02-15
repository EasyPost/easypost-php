<?php

namespace EasyPost;

/**
 * @package EasyPost
 * @property string $id
 * @property string $object
 * @property string $mode
 * @property string $street1
 * @property string $street2
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property string $country
 * @property bool $residential
 * @property string $carrier_facility
 * @property string $name
 * @property string $company
 * @property string $phone
 * @property string $email
 * @property string $federal_tax_id
 * @property string $state_tax_id
 * @property Verifications|array $verifications
 */
class Address extends EasyPostObject
{
    // TODO: Once we drop support for PHP 7.4, we can use union typing which will allow us to assign
    // types to all model properties. Until then, we can't/shouldn't since it will be half-baked
    public $id;
    public $object;
    public $mode;
    public $street1;
    public $street2;
    public $city;
    public $state;
    public $zip;
    public $country;
    public $residential;
    public $carrier_facility;
    public $name;
    public $company;
    public $phone;
    public $email;
    public $federal_tax_id;
    public $state_tax_id;
    public $verifications;
}
