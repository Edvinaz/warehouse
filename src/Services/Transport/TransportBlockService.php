<?php
declare(strict_types=1);

namespace App\Services\Transport;

use App\Helpers\DateInterval;
use App\Services\TransportService;

class TransportBlockService extends TransportService
{
    public function getBlockList()
    {
        $list = $this->repository->findAll();
        $date = new DateInterval();
        $array = [];
        foreach($list as $item) {
            
            if ($date->inRange($item->getInsurance()) || !is_null($item->getRoadTax()) && $date->inRange($item->getRoadTax())) {
                $array[$item->getLicensePlate()]['insurance'] = $item->getInsurance();
                $array[$item->getLicensePlate()]['insurancedays'] = $date->differenceToday($item->getInsurance());
                $array[$item->getLicensePlate()]['roadtax'] = $item->getRoadTax();
                if(!is_null($item->getRoadTax())) {
                    $array[$item->getLicensePlate()]['roadtaxdays'] = $date->differenceToday($item->getRoadTax());
                }
            }
        }
        return $array;
    }
}