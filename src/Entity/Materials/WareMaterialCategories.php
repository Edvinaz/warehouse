<?php

namespace App\Entity\Materials;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WareMaterialCategoriesRepository")
 */
class WareMaterialCategories
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
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Materials\WareMaterials", mappedBy="category", orphanRemoval=true)
     */
    private $wareMaterials;

    public function __construct()
    {
        $this->wareMaterials = new ArrayCollection();
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

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|WareMaterials[]
     */
    public function getWareMaterials(): Collection
    {
        return $this->wareMaterials;
    }

    public function addWareMaterial(WareMaterials $wareMaterial): self
    {
        if (!$this->wareMaterials->contains($wareMaterial)) {
            $this->wareMaterials[] = $wareMaterial;
            $wareMaterial->setCategory($this);
        }

        return $this;
    }

    public function removeWareMaterial(WareMaterials $wareMaterial): self
    {
        if ($this->wareMaterials->contains($wareMaterial)) {
            $this->wareMaterials->removeElement($wareMaterial);
            // set the owning side to null (unless already changed)
            if ($wareMaterial->getCategory() === $this) {
                $wareMaterial->setCategory(null);
            }
        }

        return $this;
    }
}
