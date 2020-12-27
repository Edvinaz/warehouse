<?php

namespace App\Entity\Transport;

use App\Helpers\DateInterval;
use Error;
use DateTime;
use App\Helpers\TransportCost;
use App\Helpers\TransportInfo;
use Doctrine\ORM\Mapping as ORM;
use App\Helpers\TransportSummary;
use App\Models\Transport\TransportDetails;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TransportRepository")
 */
class Transport extends \App\Models\Transport\Transport
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=12)
     */
    private $licensePlate;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $brand;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $model;

    /**
     * @ORM\Column(type="text")
     */
    private $class;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transport\MonthlyUsage", mappedBy="transport")
     */
    private $monthlyUsages;

    public function __construct()
    {
        $this->monthlyUsages = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getLicensePlate().' '.$this->getBrand().' '.$this->getModel();
    }

    public function unsetMonthlyUsages()
    {
        unset($this->monthlyUsages);

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLicensePlate(): ?string
    {
        return $this->licensePlate;
    }

    public function setLicensePlate(string $licensePlate): self
    {
        $this->licensePlate = $licensePlate;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getClass(): TransportDetails
    {
        if (is_null($this->class)) {
            return new TransportDetails();
        }

        return unserialize($this->class);
    }

    public function setClass(TransportDetails $class): self
    {
        $this->class = serialize($class);

        return $this;
    }

    /**
     * @return Collection|MonthlyUsage[]
     */
    public function getMonthlyUsages(): Collection
    {
        return $this->monthlyUsages;
    }

    public function addMonthlyUsage(MonthlyUsage $monthlyUsage): self
    {
        if (!$this->monthlyUsages->contains($monthlyUsage)) {
            $this->monthlyUsages[] = $monthlyUsage;
            $monthlyUsage->setTransport($this);
        }

        return $this;
    }

    public function removeMonthlyUsage(MonthlyUsage $monthlyUsage): self
    {
        if ($this->monthlyUsages->contains($monthlyUsage)) {
            $this->monthlyUsages->removeElement($monthlyUsage);
            // set the owning side to null (unless already changed)
            if ($monthlyUsage->getTransport() === $this) {
                $monthlyUsage->setTransport(null);
            }
        }

        return $this;
    }

    public function hasMonthlyUsage(): bool
    {
        $date = new DateInterval();
        foreach($this->monthlyUsages as $usage) {
            if($date->inThisMonth($usage->getDate())) {
                return true;
            }
        }
        return false;
    }

    public function isMonthlyUsageClosed(): bool
    {
        $date = new DateInterval();
        foreach ($this->monthlyUsages as $usage) {
            if ($date->inThisMonth($usage->getDate())) {
                if ($usage->isClosed()) {
                    return true;
                }
                return false;
            }
        }
        return false;
    }

    public function getInsurance()
    {
        return $this->getClass()->getInsurance();
    }

    public function getRoadTax()
    {
        return $this->getClass()->getRoadTax();
    }

    public function updateCosts(TransportCost $cost, string $date)
    {
        $details = $this->getClass();
        $details->setCosts($cost, $date);
        $this->setClass($details);
        // dd($this->getClass());

        return $this;
    }

    public function getCosts(string $date)
    {
        return $this->getClass()->getCosts($date);
    }

    public function getAllCosts()
    {
        return $this->getClass()->allCosts();
    }

    public function setPurchasedFuel(array $fuel)
    {
        $details = $this->getClass();
        $details->setUsedFuel($fuel);
        $this->setClass($details);

        return $this;
    }

    public function unsetPurchasedFuel(array $fuel)
    {
        $details = $this->getClass();
        $details->unsetUsedFuel($fuel);
        $this->setClass($details);

        return $this;
    }

    public function getMonthFuel($month)
    {
        $details = $this->getClass();

        return $details->getFuelMonth($month);
    }

    public function getMainFuel()
    {
        return $this->getClass()->getMainFuel();
    }

    public function getSecondaryFuel()
    {
        return $this->getClass()->getSecondaryFuel();
    }

    public function getFuelTanks()
    {
        return $this->getClass()->getFuelTanks();
    }

    public function createEmptyUsage(string $month, int $tachometerStart, int $tachometerEnd, ?int $mainTank, ?int $secondaryTank)
    {
        // return $this->getClass()->createEmptyUsage($month, $tachometerStart, $tachometerEnd, $mainTank, $secondaryTank);
    }

    public function usage(TransportInfo $info)
    {
        $class = $this->getClass()->updateUsage($info);
        $this->setClass($class);

        return $this;
    }

    public function getFuelUsage()
    {
        return $this->getClass()->getFuelUsage();
    }

    public function getTachometerPurchased()
    {
        if (is_null($this->getClass()->getTachometerPurchased())) {
            return 0;
        }
        return $this->getClass()->getTachometerPurchased();
    }

    public function getSecondaryFuelIndex()
    {
        return $this->getClass()->getSecondaryFuelIndex();
    }

    public function setTachometerPurchased(?int $tachometer)
    {
        $this->setClass($this->getClass()->setTachometerPurchased($tachometer));

        return $this;
    }

    public function getMonthlyStatistic()
    {
        $summary = new TransportSummary($this->getClass());
        $session = new Session();
        $month = $session->get('interval')->getDate();

        return $summary->getDetails($month->format('Y-m'));
    }

    public function setFuelTankStatus(array $status)
    {
        $class = $this->getClass();
        $class->setMainFuelTank($status['mainFuel']);
        $class->setSecondaryFuelTank($status['secondaryFuel']);
        $class->setTachometerPurchased($status['tachometer']);

        $this->setClass($class);

        return $this;
    }

    public function setFuelUsageArray(): self
    {
        $class = $this->getClass();
        $class->setFuelUsageArray($this);
        $this->setClass($class);

        return $this;
    }

    public function setFuelUsagePeriodStart(string $date): self
    {
        $class = $this->getClass();
        $class->setFuelUsagePeriodStart(new DateTime($date));
        $this->setClass($class);

        return $this;
    }

    public function getFuelUsagePeriodStart(): DateTime
    {
        return $this->getClass()->getFuelUsagePeriodStart();
    }

    public function setMonthFuelUsage(string $month, array $usage): self
    {
        $class = $this->getClass();
        $class->setMonthFuelUsage($month, $usage);
        $this->setClass($class);

        return $this;
    }

    /**
     * return purchased main fuel array
     *
     * @return array
     */
    public function getPurchasedMainFuel(): array
    {
        return $this->getClass()->getPurchasedMainFuel();
    }

    public function getMainFuelName()
    {
        return $this->getFuelName($this->getMainFuel());
    }

    public function getSecondaryFuelName()
    {
        return $this->getFuelName($this->getSecondaryFuel());
    }

    private function getFuelName(int $fuel): string
    {
        switch($fuel) {
            case 21:
                return 'benzinas';
            break;
            case 22:
                return 'dyzelinas';
            break;
            case 23:
                return 'dujos';
            break;
            default:
                throw new Error('Unknown fuel type');
        }
    }

    public function setDefaultFuelNorm(float $fuelNorm): self
    {
        $class = $this->getClass();
        $class->setDefaultFuelNorm($fuelNorm);
        $this->setClass($class);

        return $this;
    }

    public function getDefaultFuelNorm(): float
    {
        return $this->getClass()->getDefaultFuelNorm();
    }
}
