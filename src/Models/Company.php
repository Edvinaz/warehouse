<?php

declare(strict_types=1);

namespace App\Models;

/**
 * require details.json file in public folder with company details
 */

use App\Helpers\NameConverterLT;

class Company
{
    private $logo;
    private $name;
    private $street;
    private $postalCode;
    private $city;
    private $code;
    private $vat;
    private $phone;
    private $mobile;
    private $email;
    private $position;
    private $boss;
    private $bank;
    private $bankCode;
    private $account;

    public function __construct()
    {
        $companyDetails = json_decode(file_get_contents('details.json'));
        $image = 'images/logo.png';
        $imageData = base64_encode(file_get_contents($image));
        $this->logo = 'data:'.mime_content_type($image).';base64,'.$imageData;
        $this->name = $companyDetails->name;
        $this->street = $companyDetails->street;
        $this->postalCode = $companyDetails->postal_code;
        $this->city = $companyDetails->city;
        $this->code = $companyDetails->company_code;
        $this->vat = $companyDetails->vat_code;
        $this->phone = $companyDetails->phone;
        $this->mobile = $companyDetails->mobile;
        $this->email = $companyDetails->email;
        $this->position = $companyDetails->position;
        $this->boss = $companyDetails->boss;
        $this->bank = $companyDetails->bank;
        $this->bankCode = $companyDetails->bankCode;
        $this->account = $companyDetails->account;
    }

    /**
     * Get the value of name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the value of address.
     */
    public function getAddress()
    {
        return $this->street.', '.$this->postalCode.' '.$this->city;
    }

    /**
     * Get the value of code.
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get the value of vat.
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * Get the value of phone.
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Get the value of email.
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Get the value of position.
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Get the value of boss.
     */
    public function getBoss()
    {
        return $this->boss;
    }

    /**
     * Get the value of account.
     */
    public function getAccount()
    {
        return 'a/s '.$this->account;
    }

    /**
     * Get the value of mobile.
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * Get the value of bank.
     */
    public function getBank()
    {
        return $this->bank;
    }

    /**
     * Get the value of bankCode.
     */
    public function getBankCode()
    {
        return 'banko kodas '.$this->bankCode;
    }

    /**
     * Get the value of logo.
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Get the value of street.
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Get the value of postalCode.
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Get the value of city.
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Get the value of ceo.
     */
    public function getCeo()
    {
        $converter = new NameConverterLT();
        return $converter->convertString($this->position.' '.$this->boss);
    }
}
