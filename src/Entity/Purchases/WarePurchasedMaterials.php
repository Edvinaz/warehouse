<?php

namespace App\Entity\Purchases;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Objects\WareObjects;
use App\Entity\Purchases\WareInvoices;
use App\Entity\Materials\WareMaterials;
use Doctrine\Common\Collections\Collection;
use App\Entity\Debition\WareDebitedMaterials;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WarePurchasedMaterialsRepository")
 */
class WarePurchasedMaterials
{
    protected $events = [];
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Purchases\WareInvoices", inversedBy="warePurchasedMaterials")
     */
    private $invoice;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Materials\WareMaterials", inversedBy="warePurchasedMaterials")
     * @ORM\JoinColumn(nullable=false)
     */
    private $material;

    /**
     * @Assert\PositiveOrZero()
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $quantity;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=5)
     */
    private $price;

    /**
     * @ORM\Column(type="integer")
     */
    private $vat;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $balance;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Objects\WareObjects", inversedBy="warePurchasedMaterials")
     */
    private $object;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Debition\WareDebitedMaterials", mappedBy="purchase", orphanRemoval=false)
     */
    private $wareDebitedMaterials;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $note;

    public function __construct()
    {
        $this->wareDebitedMaterials = new ArrayCollection();
        $this->raise(['create' => 'new material']);
    }

    public function __toString()
    {
        return (string) $this->material.' ('.$this->invoice->getDate()->format('Y-m-d').'/'.$this->quantity.' - '.$this->price.' Eur/vnt )';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInvoice(): ?WareInvoices
    {
        return $this->invoice;
    }

    public function setInvoice(?WareInvoices $invoice): self
    {
        $this->invoice = $invoice;

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

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(string $quantity): self
    {
        $quantity = \str_replace(',', '.', $quantity);
        $this->raise(['quantity' => [$this->quantity, $quantity, ($quantity - $this->quantity)]]);
        $this->quantity = $quantity;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $price = \str_replace(',', '.', $price);
        $event = ['price' => [$this->getPrice(), $price, (string) (floatval($price) - floatval($this->getPrice()))]];

        $this->raise($event);

        $this->price = $price;

        return $this;
    }

    public function getVat(): ?int
    {
        return $this->vat;
    }

    public function setVat(int $vat): self
    {
        $this->vat = $vat;

        return $this;
    }

    public function getBalance(): ?string
    {
        return $this->balance;
    }

    public function setBalance(string $balance): self
    {
        $this->balance = \str_replace(',', '.', $balance);

        return $this;
    }

    public function decreaseBalance(string $decrease): self
    {
        $this->raise(['balance decrease' => (string) (floatval($decrease) * floatval($this->getPrice()))]);
        $this->balance -= $decrease;

        return $this;
    }

    public function increaseBalance(string $increase): self
    {
        $this->raise(['balance' => $increase]);
        $this->balance += floatval($increase);

        return $this;
    }

    public function getObject(): ?WareObjects
    {
        return $this->object;
    }

    public function setObject(?WareObjects $object): self
    {
        $this->raise(['object' => $this->getObject()]);

        $this->object = $object;

        return $this;
    }

    public function getSum()
    {
        return $this->getPrice() * $this->balance;
    }

    public function getVatSum()
    {
        return $this->getPrice() * $this->quantity * ($this->vat / 100);
    }

    public function isReserved(): bool
    {
        if (\is_null($this->getObject())) {
            return false;
        }

        return true;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('quantity', new Assert\PositiveOrZero());
        $metadata->addPropertyConstraint('price', new Assert\Positive());
    }

    /**
     * @return Collection|WareDebitedMaterials[]
     */
    public function getWareDebitedMaterials(): Collection
    {
        return $this->wareDebitedMaterials;
    }

    public function isDebited(): bool
    {
        if (count($this->wareDebitedMaterials) > 0) {
            return true;
        }

        return false;
    }

    public function addWareDebitedMaterial(WareDebitedMaterials $wareDebitedMaterial): self
    {
        if (!$this->wareDebitedMaterials->contains($wareDebitedMaterial)) {
            $this->wareDebitedMaterials[] = $wareDebitedMaterial;
            $wareDebitedMaterial->setPurchase($this);
        }

        return $this;
    }

    public function removeWareDebitedMaterial(WareDebitedMaterials $wareDebitedMaterial): self
    {
        if ($this->wareDebitedMaterials->contains($wareDebitedMaterial)) {
            $this->wareDebitedMaterials->removeElement($wareDebitedMaterial);
            if ($wareDebitedMaterial->getPurchase() === $this) {
                $wareDebitedMaterial->setPurchase(null);
            }
        }

        return $this;
    }

    /**
     * Note for using with fuel [0 => transportID, 1 => tachometer, 2 => date]
     *
     * @return array|null
     */
    public function getNote(): ?array
    {
        return explode(',', $this->note);
    }

    public function setNote(?string $note): self
    {
        // if (!is_null($this->note)) {
        //     $this->raise(['old_note' => $this->note]);
        // }
        $this->raise(['note' => [$this->note, $note]]);
        $this->note = $note;

        return $this;
    }

    public function getType(): int
    {
        return $this->getMaterial()->getCategory()->getType();
    }

    public function getCategory(): int
    {
        return $this->getMaterial()->getCategory()->getId();
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

    public function getDate(): DateTimeInterface
    {
        return $this->getInvoice()->getDate();
    }

    public function getMaterialName()
    {
        return $this->getMaterial()->getName();
    }

    public function getPurchaseDate()
    {
        return $this->getInvoice()->getDate();
    }

    protected function raise($event)
    {
        $this->events[key($event)] = $event[key($event)];
    }
}
