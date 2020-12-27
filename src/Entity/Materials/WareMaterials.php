<?php

namespace App\Entity\Materials;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Sales\BuhInvoiceContent;
use Doctrine\Common\Collections\Collection;
use App\Entity\Purchases\WarePurchasedMaterials;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WareMaterialsRepository")
 */
class WareMaterials
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $unit;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Materials\WareMaterialCategories", inversedBy="wareMaterials")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Purchases\WarePurchasedMaterials", mappedBy="material", orphanRemoval=true)
     */
    private $warePurchasedMaterials;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sales\BuhInvoiceContent", mappedBy="material")
     */
    private $buhInvoiceContents;

    public function __construct()
    {
        $this->warePurchasedMaterials = new ArrayCollection();
        $this->buhInvoiceContents = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getCategory(): ?WareMaterialCategories
    {
        return $this->category;
    }

    public function setCategory(?WareMaterialCategories $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|WarePurchasedMaterials[]
     */
    public function getWarePurchasedMaterials(): Collection
    {
        return $this->warePurchasedMaterials;
    }

    public function addWarePurchasedMaterial(WarePurchasedMaterials $warePurchasedMaterial): self
    {
        if (!$this->warePurchasedMaterials->contains($warePurchasedMaterial)) {
            $this->warePurchasedMaterials[] = $warePurchasedMaterial;
            $warePurchasedMaterial->setMaterial($this);
        }

        return $this;
    }

    public function removeWarePurchasedMaterial(WarePurchasedMaterials $warePurchasedMaterial): self
    {
        if ($this->warePurchasedMaterials->contains($warePurchasedMaterial)) {
            $this->warePurchasedMaterials->removeElement($warePurchasedMaterial);
            // set the owning side to null (unless already changed)
            if ($warePurchasedMaterial->getMaterial() === $this) {
                $warePurchasedMaterial->setMaterial(null);
            }
        }

        return $this;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addConstraint(new UniqueEntity([
            'fields' => 'name',
        ]));
        $metadata->addPropertyConstraint('name', new NotBlank());
        $metadata->addPropertyConstraint('unit', new NotBlank());
    }

    /**
     * @return Collection|BuhInvoiceContent[]
     */
    public function getBuhInvoiceContents(): Collection
    {
        return $this->buhInvoiceContents;
    }

    public function addBuhInvoiceContent(BuhInvoiceContent $buhInvoiceContent): self
    {
        if (!$this->buhInvoiceContents->contains($buhInvoiceContent)) {
            $this->buhInvoiceContents[] = $buhInvoiceContent;
            $buhInvoiceContent->setMaterial($this);
        }

        return $this;
    }

    public function removeBuhInvoiceContent(BuhInvoiceContent $buhInvoiceContent): self
    {
        if ($this->buhInvoiceContents->contains($buhInvoiceContent)) {
            $this->buhInvoiceContents->removeElement($buhInvoiceContent);
            // set the owning side to null (unless already changed)
            if ($buhInvoiceContent->getMaterial() === $this) {
                $buhInvoiceContent->setMaterial(null);
            }
        }

        return $this;
    }

    public function getCategoryType(): ?int
    {
        return $this->getCategory()->getType();
    }
}
