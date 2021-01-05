<?php
declare(strict_types=1);

namespace App\Services\Objects;

use App\Services\ObjectsService;
use App\Entity\Sales\BuhInvoices;
use App\Entity\Debition\WareWriteOffs;
use App\Entity\Sales\BuhInvoiceContent;

class ObjectInvoiceService extends ObjectsService
{
    public function getInvoice(int $objectId, ?int $invoiceId)
    {
        $object = $this->getObject($objectId);
        foreach ($object->getBuhInvoices() as $invoice) {
            if ($invoice->getId() === $invoiceId) {
                return $invoice;
            }
        }
        $lastInvoice = $this->invoicesRepository->getLastInvoice()[0];
        $invoice = new BuhInvoices();
        $invoice->setNumber($lastInvoice->getNumber() + 1);
        $invoice->setDate($lastInvoice->getDate());
        $invoice->setObject($object);
        $invoice->setContrahent($object->getContrahent());

        return $invoice;
    }

    public function saveInvoice(BuhInvoices $invoice): void
    {
        $savedInvoice = $this->invoicesRepository->save($invoice);
        $this->saveWriteOff($savedInvoice);
    }

    public function getInvoiceContent(int $objectId, int $invoiceId)
    {
        $invoice = $this->getInvoice($objectId, $invoiceId);
        $content = new BuhInvoiceContent();
        $content->setInvoice($invoice);
        $content->setObject($this->getObject($objectId));

        return $content;
    }

    public function saveInvoiceContent(BuhInvoiceContent $content)
    {
        $invoice = $content->getInvoice();
        $invoice->addTotal($content->getSum());
        $this->invoiceContentRepository->save($content);
    }

    public function deleteInvoiceContent(int $contentId)
    {
        $content = $this->invoiceContentRepository->find($contentId);
        $invoice = $content->getInvoice();
        $invoice->cutTotal($content->getSum());
        $this->invoiceContentRepository->delete($content);
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
