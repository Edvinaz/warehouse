<?php

declare(strict_types=1);

namespace App\Models;

class Company
{
    private $logo;
    private $name;
    private $address;
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
    private $ceo;

    public function __construct()
    {
        $image = 'images/logo.png';
        $imageData = base64_encode(file_get_contents($image));
        $this->logo = 'data:'.mime_content_type($image).';base64,'.$imageData;
        $this->name = 'Relsta, UAB';
        $this->address = 'Pienių g. 12, LT-47444 Kaunas';
        $this->street = 'Pienių g. 12';
        $this->postalCode = 'LT-47444';
        $this->city = 'Kaunas';
        $this->code = '134765264';
        $this->vat = 'LT347652610';
        $this->phone = '+370 37 489162';
        $this->mobile = '+370 698 39258';
        $this->email = 'info@relsta.lt';
        $this->position = 'direktorius';
        $this->boss = 'Romualdas Pinkevičius';
        $this->bank = 'AB bankas Swedbank';
        $this->bankCode = 'banko kodas 73000';
        $this->account = 'a/s LT067300010002275665';
        $this->ceo = 'direktoriaus Romualdo Pinkevičiaus';
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
        return $this->address;
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
        return $this->account;
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
        return $this->bankCode;
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
        return $this->ceo;
    }
}
