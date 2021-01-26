<?php
declare(strict_types=1);

namespace App\Models;

use App\Entity\Contrahents;
use App\Helpers\ToWordsLT;
use App\Entity\Sales\BuhInvoices;
use DateTimeInterface;

class Form3
{
    private $invoice;
    private $object;
    private $contract;

    private $contractTotal;
    private $previousTotal;
    private $invoiceTotal;
    private $doneTotal;
    private $remainTotal;

    public function __construct(BuhInvoices $buhInvoices)
    {
        $this->invoice = $buhInvoices;
        $this->object = $buhInvoices->getObject();
        $this->contract = $this->object->getBuhContracts();
        $this->setAll();
    }

    private function setAll()
    {
        $contractTotal = $this->contract->getTotal();
        $invoiceTotal = $this->invoice->getTotal();
        $previousTotal = 1709.37;

        $this->contractTotal = [
            'total' => $contractTotal,
            'VAT' => $contractTotal * 0.21,
            'withVAT' => $contractTotal * 1.21
        ];
        
        $this->previousTotal = [
            'total' => $previousTotal,
            'VAT' => $previousTotal * 0.21,
            'withVAT' => $previousTotal * 1.21
        ];

        $this->invoiceTotal = [
            'total' => $invoiceTotal,
            'VAT' => $invoiceTotal * 0.21,
            'withVAT' => $invoiceTotal * 1.21
        ];

        $this->doneTotal = [
            'total' => $this->previousTotal['total'] + $this->invoiceTotal['total'],
            'VAT' => $this->previousTotal['VAT'] + $this->invoiceTotal['VAT'],
            'withVAT' => $this->previousTotal['withVAT'] + $this->invoiceTotal['withVAT']
        ];

        $this->remainTotal = [
            'total' => $this->contractTotal['total'] - $this->doneTotal['total'],
            'VAT' => $this->contractTotal['VAT'] - $this->doneTotal['VAT'],
            'withVAT' => $this->contractTotal['withVAT'] - $this->doneTotal['withVAT']
        ];

        return $this;
    }

    public function getNumber(): string
    {
        return (string) $this->invoice->getNumber();
    }

    public function getDate(): DateTimeInterface
    {
        return $this->invoice->getDate();
    }

    public function getDateInWords()
    {
        return $this->invoice->getDate()->format('Y').' m. '.$this->invoice->month().' mėn. ';
    }

    public function getContrahent(): Contrahents
    {
        return $this->invoice->getContrahent();
    }

    public function getObject(): string
    {
        return $this->object->getName().', '.$this->object->getAdress();
    }

    public function getContract(): array
    {
        return $this->contractTotal;
    }

    public function getInvoice(): array
    { 
        return $this->invoiceTotal;
    }

    public function getPrevious(): array
    {
        return $this->previousTotal;
    }

    public function getDone(): array
    {
        return $this->doneTotal;
    }

    public function getRemain(): array
    {
        return $this->remainTotal;
    }

    public function getInvoiceTotalWords(): string
    {
        $word = new ToWordsLT();
        $sum = (string) number_format($this->invoiceTotal['withVAT'], 2, '.', '');
        $total = explode('.', $sum);
        return number_format($this->invoiceTotal['withVAT'], 2, ',', ' ').' Eur ( '.$word->toWordsLT($sum).' Eur '.$total[1].' ct )';
    }
    
    public function getFullObject(): string
    {
        return $this->object->getName().', '.$this->object->getAdress().' pagal '.$this->object->getObjectContractName().' Nr.'.$this->contract->getNumber().', '.$this->contract->getDateInWords();
    }
}