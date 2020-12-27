<?php

namespace App\Entity\Debition;

use App\Entity\Sales\BuhInvoices;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Objects\WareObjects;
use Doctrine\Common\Collections\Collection;
use App\Entity\Debition\WareDebitedMaterials;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WareWriteOffsRepository")
 */
class WareWriteOffs
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Objects\WareObjects", inversedBy="wareWriteOffs")
     */
    private $object;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Sales\BuhInvoices", cascade={"persist", "remove"})
     */
    private $invoice;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $number;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $amount;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Debition\WareDebitedMaterials", mappedBy="writeoff", orphanRemoval=true)
     */
    private $wareDebitedMaterials;

    public function __construct()
    {
        $this->wareDebitedMaterials = new ArrayCollection();
    }

    public function __toString()
    {
        return (string) $this->number;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getInvoice(): ?BuhInvoices
    {
        return $this->invoice;
    }

    public function setInvoice(?BuhInvoices $invoice): self
    {
        $this->invoice = $invoice;

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

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function increaseAmount(string $amount): self
    {
        $this->amount += $amount;

        return $this;
    }

    public function decreaseAmount(float $amount): self
    {
        $this->amount -= $amount;

        return $this;
    }

    public function isObject(): bool
    {
        if (\is_null($this->getObject())) {
            return false;
        }

        return true;
    }

    /**
     * @return Collection|WareDebitedMaterials[]
     */
    public function getWareDebitedMaterials(): Collection
    {
        return $this->wareDebitedMaterials;
    }

    public function addWareDebitedMaterial(WareDebitedMaterials $wareDebitedMaterial): self
    {
        if (!$this->wareDebitedMaterials->contains($wareDebitedMaterial)) {
            $this->wareDebitedMaterials[] = $wareDebitedMaterial;
            $wareDebitedMaterial->setWriteoff($this);
        }

        return $this;
    }

    public function removeWareDebitedMaterial(WareDebitedMaterials $wareDebitedMaterial): self
    {
        if ($this->wareDebitedMaterials->contains($wareDebitedMaterial)) {
            $this->wareDebitedMaterials->removeElement($wareDebitedMaterial);
            // set the owning side to null (unless already changed)
            if ($wareDebitedMaterial->getWriteoff() === $this) {
                $wareDebitedMaterial->setWriteoff(null);
            }
        }

        return $this;
    }

    public function getDebitedMaterialsAmount()
    {
        $amount = 0;
        foreach ($this->wareDebitedMaterials as $debit) {
            $amount += $debit->getTotalSum();
        }
        return $amount;
    }
}
