<?php

namespace App\Entity\Objects;

use App\Entity\Sales\BuhInvoices;
use App\Entity\Contrahents;
use App\Entity\Sales\BuhContracts;
use App\Models\ObjectDetails;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Sales\BuhInvoiceContent;
use App\Entity\Debition\WareWriteOffs;
use App\Entity\Purchases\WareInvoices;
use Doctrine\Common\Collections\Collection;
use App\Entity\Purchases\WarePurchasedMaterials;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WareObjectsRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string", length=4)
 * @ORM\DiscriminatorMap({
 *      "installation" = "InstallationObject",
 *      "measurement" = "MeasurementObject"
 * })
 */
abstract class WareObjects
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Contrahents", inversedBy="wareObjects")
     * @ORM\JoinColumn(nullable=false)
     */
    private $contrahent;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adress;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    private $number;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Purchases\WarePurchasedMaterials", mappedBy="object")
     */
    private $warePurchasedMaterials;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sales\BuhInvoices", mappedBy="object")
     */
    private $buhInvoices;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Purchases\WareInvoices", mappedBy="object")
     */
    private $wareInvoices;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Debition\WareWriteOffs", mappedBy="object")
     */
    private $wareWriteOffs;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Sales\BuhContracts", mappedBy="object")
     */
    private $buhContracts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sales\BuhInvoiceContent", mappedBy="object")
     */
    private $buhInvoiceContents;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Objects\TimeCard", mappedBy="object")
     */
    private $timeCards;

    /**
     * @ORM\Column(type="text")
     */
    private $entity;

    public function __construct()
    {
        $this->warePurchasedMaterials = new ArrayCollection();
        $this->buhInvoices = new ArrayCollection();
        $this->wareInvoices = new ArrayCollection();
        $this->wareWriteOffs = new ArrayCollection();
        $this->buhInvoiceContents = new ArrayCollection();
        $this->timeCards = new ArrayCollection();
    }

    public function __toString()
    {
        return (string) $this->number;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getHeader(): string
    {
        return $this->number.'. '.$this->name.', '.$this->adress;
    }

    public function getReserved(): ?string
    {
        return (string) $this->getEntity()->getReservedMaterials();
    }

    public function getIncome(): ?string
    {
        return $this->getEntity()->getIncome();
    }

    public function hasReserved(): bool
    {
        if ($this->warePurchasedMaterials->count() > 0) {
            return true;
        }

        return false;
    }

    public function hasInvoices(): bool
    {
        if ($this->wareWriteOffs->count() > 0) {
            return true;
        }

        return false;
    }

    /**
     * @return Collection|WarePurchasedMaterials[]
     */
    public function getWarePurchasedMaterials(): Collection
    {
        return $this->warePurchasedMaterials;
    }

    public function addWarePurchasedMaterial(
        WarePurchasedMaterials $warePurchasedMaterial
    ): self {
        if (!$this->warePurchasedMaterials->contains($warePurchasedMaterial)) {
            $this->warePurchasedMaterials[] = $warePurchasedMaterial;
            $warePurchasedMaterial->setObject($this);
        }

        return $this;
    }

    public function removeWarePurchasedMaterial(
        WarePurchasedMaterials $warePurchasedMaterial
    ): self {
        if ($this->warePurchasedMaterials->contains($warePurchasedMaterial)) {
            $this->warePurchasedMaterials->removeElement($warePurchasedMaterial);
            // set the owning side to null (unless already changed)
            if ($warePurchasedMaterial->getObject() === $this) {
                $warePurchasedMaterial->setObject(null);
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

    public function addBuhInvoice(
        BuhInvoices $buhInvoice
    ): self {
        if (!$this->buhInvoices->contains($buhInvoice)) {
            $this->buhInvoices[] = $buhInvoice;
            $buhInvoice->setObject($this);
        }

        return $this;
    }

    public function removeBuhInvoice(
        BuhInvoices $buhInvoice
    ): self {
        if ($this->buhInvoices->contains($buhInvoice)) {
            $this->buhInvoices->removeElement($buhInvoice);
            // set the owning side to null (unless already changed)
            if ($buhInvoice->getObject() === $this) {
                $buhInvoice->setObject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|WareInvoices[]
     */
    public function getWareInvoices(): Collection
    {
        return $this->wareInvoices;
    }

    public function addWareInvoice(
        WareInvoices $wareInvoice
    ): self {
        if (!$this->wareInvoices->contains($wareInvoice)) {
            $this->wareInvoices[] = $wareInvoice;
            $wareInvoice->setObject($this);
        }

        return $this;
    }

    public function removeWareInvoice(
        WareInvoices $wareInvoice
    ): self {
        if ($this->wareInvoices->contains($wareInvoice)) {
            $this->wareInvoices->removeElement($wareInvoice);
            // set the owning side to null (unless already changed)
            if ($wareInvoice->getObject() === $this) {
                $wareInvoice->setObject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|WareWriteOffs[]
     */
    public function getWareWriteOffs(): Collection
    {
        return $this->wareWriteOffs;
    }

    public function addWareWriteOff(
        WareWriteOffs $wareWriteOff
    ): self {
        if (!$this->wareWriteOffs->contains($wareWriteOff)) {
            $this->wareWriteOffs[] = $wareWriteOff;
            $wareWriteOff->setObject($this);
        }

        return $this;
    }

    public function removeWareWriteOff(
        WareWriteOffs $wareWriteOff
    ): self {
        if ($this->wareWriteOffs->contains($wareWriteOff)) {
            $this->wareWriteOffs->removeElement($wareWriteOff);
            // set the owning side to null (unless already changed)
            if ($wareWriteOff->getObject() === $this) {
                $wareWriteOff->setObject(null);
            }
        }

        return $this;
    }

    public function getBuhContracts(): ?BuhContracts
    {
        return $this->buhContracts;
    }

    public function setBuhContracts(
        BuhContracts $buhContracts
    ): self {
        $this->buhContracts = $buhContracts;

        // set the owning side of the relation if necessary
        if ($buhContracts->getObject() !== $this) {
            $buhContracts->setObject($this);
        }

        return $this;
    }

    /**
     * @return Collection|BuhInvoiceContent[]
     */
    public function getBuhInvoiceContents(): Collection
    {
        return $this->buhInvoiceContents;
    }

    public function addBuhInvoiceContent(
        BuhInvoiceContent $buhInvoiceContent
    ): self {
        if (!$this->buhInvoiceContents->contains($buhInvoiceContent)) {
            $this->buhInvoiceContents[] = $buhInvoiceContent;
            $buhInvoiceContent->setObject($this);
        }

        return $this;
    }

    public function removeBuhInvoiceContent(
        BuhInvoiceContent $buhInvoiceContent
    ): self {
        if ($this->buhInvoiceContents->contains($buhInvoiceContent)) {
            $this->buhInvoiceContents->removeElement($buhInvoiceContent);
            // set the owning side to null (unless already changed)
            if ($buhInvoiceContent->getObject() === $this) {
                $buhInvoiceContent->setObject(null);
            }
        }

        return $this;
    }

    public function hasContract(): bool
    {
        if ($this->buhContracts) {
            return true;
        }

        return false;
    }

    /**
     * @return Collection|TimeCard[]
     */
    public function getTimeCards(): Collection
    {
        return $this->timeCards;
    }

    public function addTimeCard(
        TimeCard $timeCard
    ): self {
        if (!$this->timeCards->contains($timeCard)) {
            $this->timeCards[] = $timeCard;
            $timeCard->setObject($this);
        }

        return $this;
    }

    public function removeTimeCard(
        TimeCard $timeCard
    ): self {
        if ($this->timeCards->contains($timeCard)) {
            $this->timeCards->removeElement($timeCard);
            // set the owning side to null (unless already changed)
            if ($timeCard->getObject() === $this) {
                $timeCard->setObject(null);
            }
        }

        return $this;
    }

    public function getEntity(): ?ObjectDetails
    {
        if (unserialize($this->entity)) {
            return unserialize($this->entity);
        }

        return new ObjectDetails();
    }

    public function setEntity(
        ObjectDetails $entity
    ): self {
        $this->entity = serialize($entity);

        return $this;
    }

    public function updateReservedMaterials(
        string $update
    ) {
        $details = $this->getEntity();
        $details->updateReservedMaterials($update);
        $this->setEntity($details);

        return $this;
    }

    public function updateDebitedMaterials(
        string $update
    ) {
        $details = $this->getEntity();
        $details->updateDebitedMaterials($update);
        $this->setEntity($details);

        return $this;
    }

    public function updateDebitedMaterialsFromReservedMaterials(
        string $update
    ) {
        $details = $this->getEntity();
        $details->updateDebitedMaterials($update);
        $reserve = (string) (-1 * floatval($update));
        $details->updateReservedMaterials($reserve);
        $this->setEntity($details);

        return $this;
    }

    public function updateServices(
        string $update
    ) {
        $details = $this->getEntity();
        $details->updateServices($update);
        $this->setEntity($details);

        return $this;
    }

    public function updateIncome(
        string $update
    ) {
        $details = $this->getEntity();
        $details->updateIncome($update);
        $this->setEntity($details);

        return $this;
    }

    public function getServices(): ?string
    {
        return $this->getEntity()->getServices();
    }

    public function getDebitedMaterials(): ?string
    {
        return $this->getEntity()->getDebitedMaterials();
    }

    public function getStaff(): array
    {
        return $this->getEntity()->getStaff();
    }

    public function getWorkedHours(): int
    {
        return $this->getEntity()->getWorkedHours();
    }

    public function getObjectContract(){}

    public function getObjectContractName(){}
}
