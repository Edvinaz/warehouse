<?php
declare(strict_types=1);
namespace App\Services;

use App\Entity\Purchases\WareInvoices;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\WareInvoicesRepository;
use App\Repository\WareMaterialsRepository;
use App\Repository\WarePurchasedMaterialsRepository;

class PurchaseService
{
    protected $invoicesRepository;
    protected $materialRepository;
    protected $purchasedMaterialsRepository;
    protected $em;

    public function __construct(
        WareInvoicesRepository $wareInvoicesRepository,
        WareMaterialsRepository $wareMaterialsRepository,
        WarePurchasedMaterialsRepository $warePurchasedMaterialsRepository,
        EntityManagerInterface $entityManagerInterface
    ) {
       $this->invoicesRepository = $wareInvoicesRepository;
       $this->materialRepository = $wareMaterialsRepository;
       $this->purchasedMaterialsRepository = $warePurchasedMaterialsRepository; 
       $this->em = $entityManagerInterface;
    }

    public function getInvoice(int $invoiceId): WareInvoices
    {
        return $this->invoicesRepository->find($invoiceId);
    }
}
