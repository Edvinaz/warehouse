<?php

declare(strict_types=1);

namespace App\Models;

use App\Helpers\NameConverterLT;

class Company
{
    protected $logo;
    protected $name;
    protected $street;
    protected $postalCode;
    protected $city;
    protected $code;
    protected $vat;
    protected $phone;
    protected $mobile;
    protected $email;
    protected $position;
    protected $boss;
    protected $bank;
    protected $bankCode;
    protected $account;

    public function __construct()
    {
        if (file_exists('details.json')) {
            $companyDetails = json_decode(file_get_contents('details.json'));
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

        $image = 'images/logo.png';
        $imageData = base64_encode(file_get_contents($image));
        $this->logo = 'data:'.mime_content_type($image).';base64,'.$imageData;
        
    }

    /**
     * Get the value of name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the value of address.
     */
    public function getAddress(): string
    {
        return $this->street.', '.$this->postalCode.' '.$this->city;
    }

    /**
     * Get the value of code.
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Get the value of vat.
     */
    public function getVat(): string
    {
        return $this->vat;
    }

    /**
     * Get the value of phone.
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * Get the value of email.
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Get the value of position.
     */
    public function getPosition(): string
    {
        return $this->position;
    }

    /**
     * Get the value of boss.
     */
    public function getBoss(): string
    {
        return $this->boss;
    }

    /**
     * Get the value of account.
     */
    public function getAccount(): string
    {
        return 'a/s '.$this->account;
    }   

    /**
     * Get the value of mobile.
     */
    public function getMobile(): string
    {
        return $this->mobile;
    }

    /**
     * Get the value of bank.
     */
    public function getBank(): string
    {
        return $this->bank;
    }

    /**
     * Get the value of bankCode.
     */
    public function getBankCode(): string
    {
        return 'banko kodas '.$this->bankCode;
    }

    /**
     * Get the value of logo.
     */
    public function getLogo(): string
    {
        return $this->logo;
    }

    /**
     * Get the value of street.
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * Get the value of postalCode.
     */
    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    /**
     * Get the value of city.
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * Get the value of ceo.
     */
    public function getCeo(): string
    {
        $converter = new NameConverterLT();
        return $converter->convertString($this->position.' '.$this->boss);
    }
}
