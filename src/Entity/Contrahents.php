<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Sales\BuhInvoices;
use App\Entity\Sales\BuhContracts;
use App\Entity\Objects\WareObjects;
use App\Entity\Purchases\WareInvoices;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ContrahentsRepository")
 */
class Contrahents
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
    private $companyCode;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $vatCode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adress;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $representative;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $boss;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $account;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Purchases\WareInvoices", mappedBy="contrahent", orphanRemoval=true)
     */
    private $wareInvoices;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Objects\WareObjects", mappedBy="contrahent", orphanRemoval=true)
     */
    private $wareObjects;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sales\BuhInvoices", mappedBy="contrahent")
     */
    private $buhInvoices;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sales\BuhContracts", mappedBy="contrahent")
     */
    private $buhContracts;


    public function __construct()
    {
        $this->wareInvoices = new ArrayCollection();
        $this->wareObjects = new ArrayCollection();
        $this->buhInvoices = new ArrayCollection();
        $this->buhContracts = new ArrayCollection();
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

    public function getCompanyCode(): ?int
    {
        return $this->companyCode;
    }

    public function setCompanyCode(int $companyCode): self
    {
        $this->companyCode = $companyCode;

        return $this;
    }

    public function getVatCode(): ?string
    {
        return $this->vatCode;
    }

    public function setVatCode(?string $vatCode): self
    {
        $this->vatCode = $vatCode;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getRepresentative(): ?string
    {
        return $this->representative;
    }

    public function setRepresentative(?string $representative): self
    {
        $this->representative = $representative;

        return $this;
    }

    public function getBoss(): ?string
    {
        return $this->boss;
    }

    public function setBoss(?string $boss): self
    {
        $this->boss = $boss;

        return $this;
    }

    public function getAccount(): ?string
    {
        return $this->account;
    }

    public function setAccount(?string $account): self
    {
        $this->account = $account;

        return $this;
    }

    /**
     * @return Collection|WareInvoices[]
     */
    public function getWareInvoices(): Collection
    {
        return $this->wareInvoices;
    }

    public function addWareInvoice(WareInvoices $wareInvoice): self
    {
        if (!$this->wareInvoices->contains($wareInvoice)) {
            $this->wareInvoices[] = $wareInvoice;
            $wareInvoice->setContrahent($this);
        }

        return $this;
    }

    public function removeWareInvoice(WareInvoices $wareInvoice): self
    {
        if ($this->wareInvoices->contains($wareInvoice)) {
            $this->wareInvoices->removeElement($wareInvoice);
            // set the owning side to null (unless already changed)
            if ($wareInvoice->getContrahent() === $this) {
                $wareInvoice->setContrahent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|WareObjects[]
     */
    public function getWareObjects(): Collection
    {
        return $this->wareObjects;
    }

    public function addWareObject(WareObjects $wareObject): self
    {
        if (!$this->wareObjects->contains($wareObject)) {
            $this->wareObjects[] = $wareObject;
            $wareObject->setContrahent($this);
        }

        return $this;
    }

    public function removeWareObject(WareObjects $wareObject): self
    {
        if ($this->wareObjects->contains($wareObject)) {
            $this->wareObjects->removeElement($wareObject);
            // set the owning side to null (unless already changed)
            if ($wareObject->getContrahent() === $this) {
                $wareObject->setContrahent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|BuhInvoices[]
     */
    public function getBuhInvoices(): Collection
    {
        return $this->buhInvoices;
    }

    public function addBuhInvoice(BuhInvoices $buhInvoice): self
    {
        if (!$this->buhInvoices->contains($buhInvoice)) {
            $this->buhInvoices[] = $buhInvoice;
            $buhInvoice->setContrahent($this);
        }

        return $this;
    }

    public function removeBuhInvoice(BuhInvoices $buhInvoice): self
    {
        if ($this->buhInvoices->contains($buhInvoice)) {
            $this->buhInvoices->removeElement($buhInvoice);
            // set the owning side to null (unless already changed)
            if ($buhInvoice->getContrahent() === $this) {
                $buhInvoice->setContrahent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|BuhContracts[]
     */
    public function getBuhContracts(): Collection
    {
        return $this->buhContracts;
    }

    public function addBuhContract(BuhContracts $buhContract): self
    {
        if (!$this->buhContracts->contains($buhContract)) {
            $this->buhContracts[] = $buhContract;
            $buhContract->setContrahent($this);
        }

        return $this;
    }

    public function removeBuhContract(BuhContracts $buhContract): self
    {
        if ($this->buhContracts->contains($buhContract)) {
            $this->buhContracts->removeElement($buhContract);
            // set the owning side to null (unless already changed)
            if ($buhContract->getContrahent() === $this) {
                $buhContract->setContrahent(null);
            }
        }

        return $this;
    }
}
