<?php
declare(strict_types=1);

namespace App\Models;

class CompanyUpdate extends Company
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getBankCode(): string
    {
        if (is_null($this->bankCode)) {
            return '';
        }
        return $this->bankCode;
    }

    public function getAccount(): string
    {
        if (is_null($this->account)) {
            return '';
        }
        return $this->account;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function setVat(string $vat): self
    {
        $this->vat = $vat;

        return $this;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function setMobile(string $mobile): self
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function setPosition(string $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function setBoss(string $boss): self
    {
        $this->boss = $boss;

        return $this;
    }

    public function setBank(string $bank): self
    {
        $this->bank = $bank;

        return $this;
    }

    public function setBankCode(string $bankCode): self
    {
        $this->bankCode = $bankCode;
        
        return $this;
    }

    public function setAccount(string $account): self
    {
        $this->account= $account;

        return $this;
    }
}
