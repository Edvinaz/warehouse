<?php

namespace App\Entity\Debition;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Purchases\WarePurchasedMaterials;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WareDebitedMaterialsRepository")
 */
class WareDebitedMaterials
{
    protected $events = [];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Debition\WareWriteOffs", inversedBy="wareDebitedMaterials")
     * @ORM\JoinColumn(nullable=false)
     */
    private $writeoff;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Purchases\WarePurchasedMaterials", inversedBy="wareDebitedMaterials")
     * @ORM\JoinColumn(nullable=false)
     */
    private $purchase;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $amount;

    public function __toString()
    {
        return $this->purchase->getMaterial()->getName().' - '.$this->amount;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWriteoff(): ?WareWriteOffs
    {
        return $this->writeoff;
    }

    public function setWriteoff(?WareWriteOffs $writeoff): self
    {
        $this->writeoff = $writeoff;

        return $this;
    }

    public function getPurchase(): ?WarePurchasedMaterials
    {
        return $this->purchase;
    }

    public function setPurchase(?WarePurchasedMaterials $purchase): self
    {
        $this->purchase = $purchase;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function increaseAmount(string $amount): self
    {
        $this->raise(['update' => (string) (floatval($amount) * floatval($this->getPurchase()->getPrice()))]);
        $this->amount += floatval($amount);

        return $this;
    }

    public function decreaseAmount(string $amount): self
    {
        $this->raise(['update' => (string) (-1 * floatval($amount) * floatval($this->getPurchase()->getPrice()))]);
        $this->amount -= $amount;

        return $this;
    }

    public function isAmountZero(): bool
    {
        if (0 == $this->amount) {
            return true;
        }

        return false;
    }

    public function getTotalSum(): string
    {
        return (string) (floatval($this->getPurchase()->getPrice()) * floatval($this->getAmount()));
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
