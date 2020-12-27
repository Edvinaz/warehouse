<?php

namespace App\Entity\Sales;

use App\Helpers\ToWordsLT;
use App\Entity\Contrahents;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Objects\WareObjects;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BuhContractsRepository")
 */
class BuhContracts
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=24)
     */
    private $number;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Contrahents", inversedBy="buhContracts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $contrahent;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Objects\WareObjects", inversedBy="buhContracts", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $object;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=4)
     */
    private $total;

    /**
     * @ORM\Column(type="boolean")
     */
    private $reverseVAT;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $advance;

    /**
     * @ORM\Column(type="date")
     */
    private $begin;

    /**
     * @ORM\Column(type="date")
     */
    private $end;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $billing;

    /**
     * @ORM\Column(type="string", length=24)
     */
    private $billingCondition;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $works;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $warranty;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $defaultInterest;

    /**
     * @ORM\Column(type="string", length=24)
     */
    private $fine;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $notes;

    public function __toString()
    {
        return (string) $this->number;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getContrahent(): ?Contrahents
    {
        return $this->contrahent;
    }

    public function setContrahent(?Contrahents $contrahent): self
    {
        $this->contrahent = $contrahent;

        return $this;
    }

    public function getObject(): ?WareObjects
    {
        return $this->object;
    }

    public function setObject(WareObjects $object): self
    {
        $this->object = $object;

        return $this;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(string $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getReverseVAT(): ?bool
    {
        return $this->reverseVAT;
    }

    public function setReverseVAT(bool $reverseVAT): self
    {
        $this->reverseVAT = $reverseVAT;

        return $this;
    }

    public function getAdvance(): ?string
    {
        return $this->advance;
    }

    public function setAdvance(string $advance): self
    {
        $this->advance = $advance;

        return $this;
    }

    public function getBegin(): ?\DateTimeInterface
    {
        return $this->begin;
    }

    public function setBegin(\DateTimeInterface $begin): self
    {
        $this->begin = $begin;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(\DateTimeInterface $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getBilling(): ?string
    {
        return $this->billing;
    }

    public function setBilling(string $billing): self
    {
        $this->billing = $billing;

        return $this;
    }

    public function getWorks(): ?string
    {
        return $this->works;
    }

    public function setWorks(string $works): self
    {
        $this->works = $works;

        return $this;
    }

    public function getWarranty(): ?string
    {
        return $this->warranty;
    }

    public function setWarranty(string $warranty): self
    {
        $this->warranty = $warranty;

        return $this;
    }

    public function getDefaultInterest(): ?string
    {
        return $this->defaultInterest;
    }

    public function setDefaultInterest(string $defaultInterest): self
    {
        $this->defaultInterest = $defaultInterest;

        return $this;
    }

    public function getFine(): ?string
    {
        return $this->fine;
    }

    public function setFine(string $fine): self
    {
        $this->fine = $fine;

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

    public function getBillingCondition(): ?string
    {
        return $this->billingCondition;
    }

    public function setBillingCondition(string $billingCondition): self
    {
        $this->billingCondition = $billingCondition;

        return $this;
    }

    public function getPrintName(): string
    {
        return $this->name;
    }

    public function getTotalWords(): string
    {
        $word = new ToWordsLT();

        $total = explode('.', number_format($this->total, 2, '.', ''));

        return $word->toWordsLT($total[0]).' Eur '.$total[1].' ct';
    }

    public function getTotalWithoutVatWords(): string
    {
        $word = new ToWordsLT();

        $total = explode('.', number_format(($this->total / 1.21), 2, '.', ''));

        return $word->toWordsLT($total[0]).' Eur '.$total[1].' ct';
    }

    public function getDateInWords(): string
    {
        $word = new ToWordsLT();

        return $this->date->format('Y').' m. '.$word->monthLT($this->date->format('m')).' mėn. '.$this->date->format('d').' d.';
    }
}
