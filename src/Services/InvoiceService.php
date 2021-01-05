<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Sales\BuhInvoiceContent;
use App\Entity\Sales\BuhInvoices;
use App\Entity\Objects\WareObjects;
use App\Entity\Debition\WareWriteOffs;
use App\Repository\BuhInvoiceContentRepository;
use App\Repository\BuhInvoicesRepository;
use App\Repository\WareWriteOffsRepository;

class InvoiceService
{
    protected $invoicesRepository;
    protected $contentRepository;
    protected $writeOffRepository;

    protected $list;
    protected $invoice;

    public function __construct(
        BuhInvoicesRepository $invoicesRepository,
        BuhInvoiceContentRepository $contentRepository,
        WareWriteOffsRepository $writeOffRepository
    ) {
        $this->invoicesRepository = $invoicesRepository;
        $this->contentRepository = $contentRepository;
        $this->writeOffRepository = $writeOffRepository;
        $this->setList();
    }

    public function setList(): self
    {
        $this->list = $this->invoicesRepository->findAll();

        return $this;
    }

    public function getList(): array
    {
        return $this->list;
    }

    public function getObjectInvoices(WareObjects $object): array
    {
        $list = [];
        foreach ($this->list as $invoice) {
            if ($invoice->getObject()->getId() === $object->getId()) {
                $list[] = $invoice;
            }
        }

        return $list;
    }

    public function getInvoice(int $invoiceId)
    {
        foreach ($this->list as $invoice) {
            if ($invoice->getId() === $invoiceId) {
                return $invoice;
            }
        }

        return null;
    }

    public function saveInvoice(BuhInvoices $invoice): void
    {
        $savedInvoice = $this->invoicesRepository->save($invoice);
        $this->saveWriteOff($savedInvoice);
    }

    public function saveInvoiceContent(BuhInvoiceContent $content)
    {
        $invoice = $content->getInvoice();
        $invoice->addTotal($content->getSum());
        $this->contentRepository->save($content);
    }

    public function deleteInvoiceContent(BuhInvoiceContent $content)
    {
        $invoice = $content->getInvoice();
        $invoice->cutTotal($content->getSum());
        $this->contentRepository->delete($content);
    }

    public function printInvoice(int $invoiceId)
    {
        return $this->invoicesRepository->find($invoiceId);
    }

    private function saveWriteOff(BuhInvoices $invoice): void
    {
        $writeOff = new WareWriteOffs();
        $writeOff->setObject($invoice->getObject());
        $writeOff->setInvoice($invoice);
        $writeOff->setNumber('REL '.$invoice->getNumber());
        $writeOff->setDate($invoice->getDate());
        $writeOff->setAmount(0);

        $this->writeOffRepository->save($writeOff);
    }
}
