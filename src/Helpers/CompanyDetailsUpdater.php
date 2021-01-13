<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Models\CompanyUpdate;
use Exception;
use stdClass;

class CompanyDetailsUpdater
{
    public static function updateDetails(CompanyUpdate $data)
    {
        if (file_exists('details.json')) {
            $companyDetails = json_decode(file_get_contents('details.json'));
        } else {
            $companyDetails = new stdClass();
        }
        
        $companyDetails->name = $data->getName();
        $companyDetails->company_code = $data->getCode();
        $companyDetails->vat_code = $data->getVat();
        $companyDetails->phone = $data->getPhone();
        $companyDetails->mobile = $data->getMobile();
        $companyDetails->email = $data->getEmail();
        $companyDetails->street = $data->getStreet();
        $companyDetails->city = $data->getCity();
        $companyDetails->country = 'Lithuania';
        $companyDetails->postal_code = $data->getPostalCode();
        $companyDetails->position = $data->getPosition();
        $companyDetails->boss = $data->getBoss();
        $companyDetails->bank = $data->getBank();
        $companyDetails->bankCode = $data->getBankCode();
        $companyDetails->account = $data->getAccount();

        $json = json_encode($companyDetails);

        if (!file_put_contents('details.json', $json)) {
            throw new Exception('file not saved');
        }
    }
}
