<?php

namespace App\Entity\Purchases;

use DateTime;
use App\Entity\Contrahents;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Objects\WareObjects;
use Doctrine\Common\Collections\Collection;
use App\Entity\Purchases\WarePurchasedMaterials;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WareInvoicesRepository")
 */
class WareInvoices
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Contrahents", inversedBy="wareInvoices")
     * @ORM\JoinColumn(nullable=false)
     */
    private $contrahent;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $number;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Purchases\WarePurchasedMaterials", mappedBy="invoice")
     */
    private $warePurchasedMaterials;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $amount;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $VAT;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Objects\WareObjects", inversedBy="wareInvoices")
     */
    private $object;

    public function __construct()
    {
        $this->warePurchasedMaterials = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->number;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection|WarePurchasedMaterials[]
     */
    public function getWarePurchasedMaterials()
    {
        return $this->warePurchasedMaterials;
    }

    public function addWarePurchasedMaterial(WarePurchasedMaterials $warePurchasedMaterial): self
    {
        if (!$this->warePurchasedMaterials->contains($warePurchasedMaterial)) {
            $this->warePurchasedMaterials[] = $warePurchasedMaterial;
            $warePurchasedMaterial->setInvoice($this);
        }

        return $this;
    }

    public function removeWarePurchasedMaterial(WarePurchasedMaterials $warePurchasedMaterial): self
    {
        if ($this->warePurchasedMaterials->contains($warePurchasedMaterial)) {
            $this->warePurchasedMaterials->removeElement($warePurchasedMaterial);
            // set the owning side to null (unless already changed)
            if ($warePurchasedMaterial->getInvoice() === $this) {
                $warePurchasedMaterial->setInvoice(null);
            }
        }

        return $this;
    }

    public function getAmount(): ?string
    {
        return (string) $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function updateAmount(string $amount): self
    {
        $this->amount += $amount;

        return $this;
    }

    public function updateVAT(string $vat): self
    {
        $this->VAT += $vat;

        return $this;
    }

    public function getVAT(): ?string
    {
        return $this->VAT;
    }

    public function setVAT(string $VAT): self
    {
        $this->VAT = $VAT;

        return $this;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addConstraint(new UniqueEntity(['fields' => 'number']));
        $metadata->addPropertyConstraint('number', new NotBlank());
        $metadata->addPropertyConstraint('date', new NotBlank());
        $metadata->addPropertyConstraint('date', new Type(DateTime::class));
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

    public function isDebited(): bool
    {
        foreach ($this->warePurchasedMaterials as $material) {
            if ($material->isDebited()){
                return true;
            }
        }
        return false;
    }
}
