<?php

declare(strict_types=1);

namespace App\Services;

use App\Repository\WareDebitedMaterialsRepository;
use App\Repository\WareObjectsRepository;
use App\Repository\WarePurchasedMaterialsRepository;
use App\Repository\WareWriteOffsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class WriteOffService
{
    protected $writeOffRepository;
    protected $objectsRepository;
    protected $debitedMaterialsRepository;
    protected $purchasedMaterialsRepository;
    protected $entityManager;
    protected $session;

    public function __construct(
        WareWriteOffsRepository $offsRepository,
        WareObjectsRepository $wareObjectsRepository,
        WareDebitedMaterialsRepository $wareDebitedMaterialsRepository,
        WarePurchasedMaterialsRepository $warePurchasedMaterialsRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->writeOffRepository = $offsRepository;
        $this->objectsRepository = $wareObjectsRepository;
        $this->debitedMaterialsRepository = $wareDebitedMaterialsRepository;
        $this->purchasedMaterialsRepository = $warePurchasedMaterialsRepository;
        $this->entityManager = $entityManager;
        $this->session = new Session();
    }

    public function getWriteOff(int $writeOffId)
    {
        return $this->writeOffRepository->find($writeOffId);
    }
}
