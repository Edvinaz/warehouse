<?php

namespace App\Entity\Sales;

use App\Helpers\ToWordsLT;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Sales\BuhInvoices;
use App\Entity\Objects\WareObjects;
use App\Entity\Materials\WareMaterials;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BuhInvoiceContentRepository")
 */
class BuhInvoiceContent
{
    protected $events = [];
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sales\BuhInvoices", inversedBy="buhInvoiceContents")
     * @ORM\JoinColumn(nullable=false)
     */
    private $invoice;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Objects\WareObjects", inversedBy="buhInvoiceContents")
     */
    private $object;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Materials\WareMaterials", inversedBy="buhInvoiceContents")
     * @ORM\JoinColumn(nullable=false)
     */
    private $material;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $amount;

    /**
     * @ORM\Column(type="decimal", precision=20, scale=5)
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $notes;

    /**
     * @ORM\Column(type="boolean")
     */
    private $depend;

    public function __toString()
    {
        return 'Content';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInvoice(): ?BuhInvoices
    {
        return $this->invoice;
    }

    public function setInvoice(?BuhInvoices $invoice): self
    {
        $this->invoice = $invoice;

        return $this;
    }

    public function getObject(): ?WareObjects
    {
        return $this->object;
    }

    public function setObject(?WareObjects $object): self
    {
        $this->object = $object;

        return $this;
    }

    public function getMaterial(): ?WareMaterials
    {
        return $this->material;
    }

    public function setMaterial(?WareMaterials $material): self
    {
        $this->material = $material;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = \str_replace(',', '.', $amount);

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->raise(['balance' => (string) (floatval($price) * floatval($this->getAmount()))]);
        $this->price = \str_replace(',', '.', $price);

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function getSum(): string
    {
        return (string) ($this->amount * $this->price);
    }

    public function getPrintObject(): ?string
    {
        if ($this->depend) {
            return 'Objektas '.$this->object->getName().', '.$this->object->getAdress().' '.$this->material->getName();
        }

        return null;
    }

    public function getPrintMaterial(): ?string
    {
        return $this->material->getName();
    }

    public function getPrintContract(): ?string
    {
        if (!is_null($this->invoice->getObject()->getBuhContracts())) {
            $word = new ToWordsLT();
            $contract = $this->invoice->getObject()->getBuhContracts();

            return 'pagal '.$contract->getPrintName().' sutartį Nr. '.$contract->getNumber().', '.$contract->getDate()->format('Y').' m. '.$word->monthLT($contract->getDate()->format('m')).' mėn. '.$contract->getDate()->format('d').' d.';
        }

        return null;
    }

    public function getDepend(): ?bool
    {
        return $this->depend;
    }

    public function setDepend(bool $depend): self
    {
        $this->depend = $depend;

        return $this;
    }

    /**
     * Work with changes.
     */
    public function popEvents(): array
    {
        $events = $this->events;

        $this->events = [];

        return $events;
    }

    protected function raise($event)
    {
        $this->events[key($event)] = $event[key($event)];
    }
}
