<?php

namespace App\Entity\Transport;

use DateTime;
use App\Helpers\MonthFuelUsage;
use App\Interfaces\FuelUsageInterface;
use Doctrine\ORM\Mapping as ORM;
use App\Models\Transport\FuelUsage;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MonthlyUsageRepository")
 */
class MonthlyUsage extends FuelUsage implements FuelUsageInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Transport\Transport", inversedBy="monthlyUsages")
     */
    private $transport;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="text")
     */
    private $class;

    /**
     * @ORM\Column(type="boolean")
     */
    private $closed;

    public function __construct(Transport $transport, DateTime $date, ?MonthlyUsage $usage)
    {
        $this->setTransport($transport);
        $this->setClass(new MonthFuelUsage($transport, $usage));
        $this->setDate($date);
        $this->setClosed(false);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTransport(): ?Transport
    {
        return $this->transport;
    }

    public function setTransport(?Transport $transport): self
    {
        $this->transport = $transport;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        $class = $this->getClass();
        $class->setPeriod($date);
        $this->setClass($class);

        return $this;
    }

    public function getClass(): MonthFuelUsage
    {
        return unserialize($this->class);
    }

    public function setClass(MonthFuelUsage $monthlyUsage): self
    {
        $this->class = serialize($monthlyUsage);
        return $this;
    }

    public function isClosed(): ?bool
    {
        return $this->closed;
    }

    public function setClosed(bool $closed): self
    {
        $this->closed = $closed;

        return $this;
    }

    public function addMainFuel(array $note, string $quantity): self
    {
        $class = $this->getClass();
        $class->setReceivedMainFuel($note, $quantity);

        $this->setClass($class);

        return $this;
    }

    public function addSecondaryFuel(array $note, string $quantity): self
    {
        $class = $this->getClass();
        $class->setReceivedSecondaryFuel($note, $quantity);

        $this->setClass($class);

        return $this;
    }

    public function removeReceivedMainFuel(array $note): self
    {
        $class = $this->getClass();
        $class->unsetReceivedMainFuel($note[2]);

        $this->setClass($class);

        return $this;
    }

    public function removeReceivedSecondaryFuel(array $note): self
    {
        $class = $this->getClass();
        $class->unsetReceivedSecondaryFuel($note[2]);

        $this->setClass($class);

        return $this;
    }

    public function getTachometerBegin(): int
    {
        return $this->getClass()->getTachometerStart();
    }

    public function getTachometerEnd(): int
    {
        return $this->getClass()->getTachometerEnd();
    }

    public function getDistance(): int
    {
        return $this->getClass()->getDistance();
    }

    public function getMonthMainFuel(): float
    {
        return $this->getClass()->getMonthMainFuel();
    }

    public function getMonthSecondaryFuel(): ?float
    {
        return $this->getClass()->getMonthSecondaryFuel();
    }

    public function getMainFuelBegin(): float
    {
        return $this->getClass()->getMainFuel()->getFuelTank();
    }

    public function getMainFuelReceived(): float
    {
        return $this->getClass()->getMainFuel()->checkReceivedFuel();
    }

    public function getMainFuelUsed(): float
    {
        return $this->getClass()->getMainFuel()->getUsedFuel();
    }

    public function getMainFuelEnd(): float
    {
        return $this->getClass()->getMainFuel()->getFuelBalance();
    }

    public function getMainFuelNorm(): float
    {
        return $this->getClass()->getMainFuel()->getFuelNorm();
    }

    public function getSecondaryFuelBegin(): float
    {
        return $this->getClass()->getSecondaryFuel()->getFuelTank();
    }

    public function getSecondaryFuelReceived(): float
    {
        return $this->getClass()->getSecondaryFuel()->checkReceivedFuel();
    }
    public function getSecondaryFuelUsed(): float
    {
        return $this->getClass()->getSecondaryFuel()->getUsedFuel();
    }
    public function getSecondaryFuelEnd(): float
    {
        return $this->getClass()->getSecondaryFuel()->getFuelBalance();
    }
    public function getSecondaryFuelNorm(): float
    {
        return $this->getClass()->getSecondaryFuel()->getFuelNorm();
    }

    public function setTachometerEnd(string $tachometer): self
    {
        $class = $this->getClass();
        $class->setTachometerEnd($tachometer);
        $this->setClass($class);

        return $this;
    }

    public function calculateUsage(float $mainFuelRemainder, ?float $secondaryFuelRemainder): self
    {
        $class = $this->getClass();
        $class->calculateUsage($mainFuelRemainder, $secondaryFuelRemainder);
        $this->setClass($class);

        return $this;
    }

    public function getEndSummary(): array
    {
        return $this->getClass()->getPeriodEndSummary();
    }

    public function updateMonthBegin(array $updates): self
    {
        $class = $this->getClass();
        $class->updateMonthBegin($updates);
        $this->setClass($class);

        return $this;
    }
}
