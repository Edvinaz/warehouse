<?php

declare(strict_types=1);

namespace App\Services;

use DateTime;
use App\Repository\Fuel\FuelRepository;

class FuelService
{
    protected $repository;
    private $list;

    public function __construct(FuelRepository $repository)
    {
        $this->repository = $repository;
        $this->list = $repository->getFuelList();
    }

    public function getList()
    {
        return $this->list;
    }

    public function getFuelStatistic()
    {
        $start = new DateTime('2019-01-01');
        $end = new DateTime('first day of this month');

        $mainList = [];

        while ($end->diff($start)->format('%R%a') < 0) {
            $list = $this->repository->getMonthStatistic($start);
            $month = [
                'benzinas' => null,
                'dyzelinas' => null,
                'dujos' => null,
            ];
            foreach ($list as $item) {
                switch($item['category']) {
                    case 'Benzinas':
                        $month['benzinas'] = $item['total'];
                    break;
                    case 'Dyzelinas':
                        $month['dyzelinas'] = $item['total'];
                    break;
                    case 'Dujos':
                        $month['dujos'] = $item['total'];
                    break;
                    default:
                break;
                }
            }
            $mainList[$start->format('Y-m')] = $month;
            $start->modify('+1 month');
        }
        // dd($mainList);
        return $mainList;
    }
}
