<?php
declare(strict_types=1);

namespace App\Factories;

use App\Services\InvoiceService;
use App\Repository\BuhInvoicesRepository;
use App\Repository\BuhInvoiceContentRepository;
use App\Repository\WareWriteOffsRepository;

class InvoiceServiceFactory
{
    private $repository;
    private $contentRepository;
    private $writeOffRepository;

    public function __construct(
        BuhInvoicesRepository $repository,
        BuhInvoiceContentRepository $contentRepository,
        WareWriteOffsRepository $writeOffRepository
    ) {
        $this->repository = $repository;
        $this->contentRepository = $contentRepository;
        $this->writeOffRepository = $writeOffRepository;
    }
    
    public function createInvoiceServiceManager(): InvoiceService
    {
        $invoiceServicrepositoryanager = 
        new InvoiceService(
            $this->repository, 
            $this->contentRepository,
            $this->writeOffRepository
        );

        return $invoiceServicrepositoryanager;
    }

}