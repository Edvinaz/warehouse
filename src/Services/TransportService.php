<?php

declare(strict_types=1);

namespace App\Services;

use App\Repository\MonthlyUsageRepository;
use App\Repository\TransportRepository;
use App\Repository\WarePurchasedMaterialsRepository;
use Symfony\Component\HttpFoundation\Session\Session;

class TransportService
{
    protected $repository;
    protected $monthlyUsageRepository;
    protected $fuelPurchaseRepository;

    protected $list;

    public function __construct(
        TransportRepository $transportRepository,
        MonthlyUsageRepository $monthlyUsageRepository,
        WarePurchasedMaterialsRepository $fuelPurchaseRepository
    ) {
        $this->repository = $transportRepository;
        $this->monthlyUsageRepository = $monthlyUsageRepository;
        $this->fuelPurchaseRepository = $fuelPurchaseRepository;

        $this->setTransportList();
    }

    /**
     * returns transport filtered list by purchase and sell date
     *
     * @return array
     */
    public function getList(): array
    {
        $session = new Session();
        $date = $session->get('interval')->getdate();

        $from = $date->format('Y-m-01');
        $to = $date->format('Y-m-t');

        $list = [];

        foreach($this->list as $auto) {
            $purchased = $auto->getClass()->getPurchased()->format('Y-m-d');
            if (!is_null($auto->getClass()->getSold())) {
                $sold = $auto->getClass()->getSold()->format('Y-m-d');
            } else {
                $sold = null;
            }
            if ($purchased < $to && is_null($sold) && $auto->hasMonthlyUsage() || $purchased < $to && !is_null($sold) && $sold > $from && $auto->hasMonthlyUsage()){
                $list[] = $auto;
            }
        }
        return $list;
    }

    /**
     * returns transport filtered list by purchase and sell date
     *
     * @return array
     */
    public function getFullList(): array
    {
        $session = new Session();
        $date = $session->get('interval')->getdate();

        $from = $date->format('Y-m-01');
        $to = $date->format('Y-m-t');

        $list = [];

        foreach($this->list as $auto) {
            $purchased = $auto->getClass()->getPurchased()->format('Y-m-d');
            if (!is_null($auto->getClass()->getSold())) {
                $sold = $auto->getClass()->getSold()->format('Y-m-d');
            } else {
                $sold = null;
            }
            if ($purchased < $to && is_null($sold) || $purchased < $to && !is_null($sold) && $sold > $from){
                $list[] = $auto;
            }
        }
        return $list;
    }

    /**
     * sets full transport list on creation
     *
     * @return self
     */
    private function setTransportList(): self
    {
        $this->list = $this->repository->findAll();

        return $this;
    }
}
