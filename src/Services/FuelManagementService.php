<?php

declare(strict_types=1);

namespace App\Services;

use Error;
use DateTime;
use DateTimeInterface;
use App\Entity\Transport\Transport;
use App\Entity\Transport\MonthlyUsage;
use App\Helpers\DateInterval;
use Exception;
use Symfony\Component\HttpFoundation\Session\Session;

class FuelManagementService extends TransportService
{
    protected $transport;

    protected $fuel;
    protected $note;

    protected $monthlyUsage;

    protected $fuelStatus;

    /**
     * set transport class
     *
     * @param integer $transportId
     * @return self
     */
    public function setTransport(int $transportId): self
    {
        foreach ($this->list as $auto) {
            if ($auto->getId() === $transportId) {
                $this->transport = $auto;
            break;
            }
        }

        return $this;
    }

    private function getTransport(int $transportId): ?Transport
    {
        foreach ($this->list as $auto) {
            if ($auto->getId() === $transportId) {
                return $auto;
            }
        }
        return null;
    }

    public function setFuel(int $fuel, ?string $tachometer)
    {
        $this->fuel = $this->fuelPurchaseRepository->find($fuel);
        $fuelNote = $this->manageFuelNote($tachometer, $fuel);

        $this->fuel->setNote(implode(',', $fuelNote));

        $this->fuelPurchaseRepository->save($this->fuel);

        if (!is_null($this->transport)) {
            $this->setMonthlyUsage($this->fuel->getInvoice()->getDate());

            $this->addPurchasedFuel();
        }

        return $this;
    }

    private function addPurchasedFuel(): void
    {
        $fuelCategory = $this->fuel->getCategory();

        if ($this->transport->getMainFuel() === $fuelCategory) {
            $this->monthlyUsage->addMainFuel($this->fuel->getNote(), $this->fuel->getQuantity());
        } else if ($this->transport->getSecondaryFuel() === $fuelCategory) {
            $this->monthlyUsage->addSecondaryFuel($this->fuel->getNote(), $this->fuel->getQuantity());
        } else {
            throw new Exception('Unacceptable fuel');
        }

        $this->monthlyUsageRepository->save($this->monthlyUsage);
    }

    private function removePurchasedFuel(array $note): void
    {
        $fuelCategory = $this->fuel->getCategory();
        $transport = $this->getTransport((int) $note[0]);
        $monthlyUsage = $this->isMonthlyUsage(new DateTime(explode(' ', $note[2])[0]), $transport);
        if ($transport->getMainFuel() === $fuelCategory) {
            $monthlyUsage->removeReceivedMainFuel($note, $this->fuel->getQuantity());
        } else if ($transport->getSecondaryFuel() === $fuelCategory) {
            $monthlyUsage->removeReceivedSecondaryFuel($note, $this->fuel->getQuantity());
        } else {
            throw new Exception('Unacceptable fuel');
        }

        $this->monthlyUsageRepository->save($monthlyUsage);
    }

    /**
    * make and returns array for purchased fuel ir warePurchasedMaterials
    *
    * @param string|null $tachometer
    * @return array
    */
    private function manageFuelNote(?string $tachometer, int $fuel): array
    {
        $note = $this->fuel->getNote();

        if (isset($note[2])){
            $date = explode(' ', $note[2])[1];
        } else {
            $date = $this->fuel->getInvoice()->getDate()->format('H:i');
        }

        if ($date === '00:00') {
            $date = $this->fuel->getInvoice()->getDate()->format('Y-m-d').' '.$fuel;
            $note = [];
        } else {
            $date = $this->fuel->getInvoice()->getDate()->format('Y-m-d H:i');
        }

        if ($this->transport) {
            $transportId = $this->transport->getId();
        } else {
            $transportId = '';
        }

        if (count($note) < 3) {
            $note[0] = $transportId;
            $note[1] = $tachometer;
            $note[2] = $date;
        } else if (empty($note[0]) || (int) $note[0] === $transportId) {
            $note[0] = $transportId;
            $note[1] = $tachometer;
        } else {
            $this->removePurchasedFuel($note);
            $note[0] = $transportId;
            $note[1] = $tachometer;
        }
        return $note;
    }

    /**
     * Sets monthly usage for current transport and month, checks existing or creates new
     *
     * @param Transport $transport
     * @param DateTime $date
     * @return void
     */
    public function setMonthlyUsage(DateTimeInterface $date): void
    {
        if (is_null($this->transport)) {
            return;
        }
        $monthlyUsage = $this->isMonthlyUsage($date, $this->transport);
        if (is_null($monthlyUsage)) {
            $this->createNewMonthlyUsage($date);
        } else {
            $monthlyUsage = $this->checkPreviousUsage($monthlyUsage);
            $this->monthlyUsage = $monthlyUsage;
        }
    }

    /**
     * Creates new MonthlyUsage class
     *
     * @param Transport $transport
     * @param DateTime $date
     * @return void
     */
    private function createNewMonthlyUsage(DateTimeInterface $date): void
    {
        $previousMonth = new DateTime($date->format('Y-m'));
        $previousMonth->modify('-1 month');

        $previousMonthUsage = $this->isMonthlyUsage($previousMonth, $this->transport);

        $purchased = $this->transport->getClass()->getPurchased();
        $sold = $this->transport->getClass()->getSold();

        if ($date->format('Y-m') === $purchased->format('Y-m') && is_null($previousMonthUsage) && (is_null($sold) || $date->format('Y-m') <= $sold->format('Y-m'))) {
            $this->monthlyUsage = new MonthlyUsage($this->transport, $date, null);
            $this->saveChanges();
        } else if ($date->format('Y-m') > $purchased->format('Y-m') && !is_null($previousMonthUsage) && (is_null($sold) || $date->format('Y-m') <= $sold->format('Y-m'))) {
            $this->monthlyUsage = new MonthlyUsage($this->transport, $date, $previousMonthUsage);
            $this->saveChanges();
        } else if ($date->format('Y-m') > $purchased->format('Y-m') && is_null($previousMonthUsage)) {
            throw new Error('Previous month usage not set');
        } else {
            throw new Error('Transport not purchased Yet');
        }
    }

    /**
     * checks is there monthly usage created, and returns monthly usage or null
     *
     * @param DateTime $date
     * @return MonthlyUsage|null
     */
    private function isMonthlyUsage(DateTimeInterface $date, Transport $transport): ?MonthlyUsage
    {
        $monthlyUsage = $this->monthlyUsageRepository->getCurrentUsage($date, $transport->getId());
        if (empty($monthlyUsage)) {
            return null;
        } else {
            return $monthlyUsage[0];
        }
    }

    private function saveChanges(): void
    {
        $this->monthlyUsageRepository->save($this->monthlyUsage);
    }

    public function setMonthTachometerEnd(string $tachometer)
    {
        $this->monthlyUsage->setTachometerEnd($tachometer);
        return $this;
    }

    public function getList(): array
    {
        $session = new Session();
        $date = $session->get('interval')->getdate();

        $from = $date->format('Y-m-01');
        $to = $date->format('Y-m-t');

        $list = [];

        foreach($this->list as $auto) {
            $purchased = $auto->getClass()->getPurchased()->format('Y-m-d');
            if (!is_null($auto->getClass()->getSold())) {
                $sold = $auto->getClass()->getSold()->format('Y-m-d');
            } else {
                $sold = null;
            }
            if ($purchased < $to && is_null($sold) || $purchased < $to && !is_null($sold) && $sold > $from){
                $usage = $this->isMonthlyUsage($date, $auto);
                if (!is_null($usage)){
                    $this->updateFuelStatus($usage);
                }
                // $suggestion = $this->calculateSuggestion($auto, $date, $usage);
                $suggestion = [];
                $list[$auto->getId()] = [$auto, $usage, $suggestion];
            }
        }
        return $list;
    }

    public function getFuelStatus()
    {
        return $this->fuelStatus;
    }

    private function updateFuelStatus(MonthlyUsage $usage)
    {
        if (is_null($this->fuelStatus)) {
            $this->fuelStatus = [
                21 => [
                    'begin' => 0,
                    'received' => 0,
                    'used' => 0,
                    'end' => 0
                ],
                22 => [
                    'begin' => 0,
                    'received' => 0,
                    'used' => 0,
                    'end' => 0
                ],
                23 => [
                    'begin' => 0,
                    'received' => 0,
                    'used' => 0,
                    'end' => 0
                ]
            ];
        }
        
        $this->fuelStatus[$usage->getTransport()->getMainFuel()]['begin'] += $usage->getMainFuelBegin();
        $this->fuelStatus[$usage->getTransport()->getMainFuel()]['received'] += $usage->getMainFuelReceived();
        $this->fuelStatus[$usage->getTransport()->getMainFuel()]['used'] += $usage->getMainFuelUsed();
        $this->fuelStatus[$usage->getTransport()->getMainFuel()]['end'] += $usage->getMainFuelEnd();

        if (!is_null($usage->getTransport()->getSecondaryFuel())) {
            $this->fuelStatus[$usage->getTransport()->getSecondaryFuel()]['begin'] += $usage->getSecondaryFuelBegin();
            $this->fuelStatus[$usage->getTransport()->getSecondaryFuel()]['received'] += $usage->getSecondaryFuelReceived();
            $this->fuelStatus[$usage->getTransport()->getSecondaryFuel()]['used'] += $usage->getSecondaryFuelUsed();
            $this->fuelStatus[$usage->getTransport()->getSecondaryFuel()]['end'] += $usage->getSecondaryFuelEnd();
        }
        return $this;
    }

    private function calculateSuggestion(Transport $auto, DateTimeInterface $date, ?MonthlyUsage $usage)
    {
        $receivedMainFuel = $usage->getMainFuelBegin() + $usage->getMainFuelReceived();
        if(!is_null($auto->getSecondaryFuel())) {
            $receivedSecondaryFuel = $usage->getSecondaryFuelBegin() + $usage->getSecondaryFuelReceived();
            $secondaryFuelUsed = $usage->getSecondaryFuelUsed();
            $index = $auto->getSecondaryFuelIndex();
        } else {
            $receivedSecondaryFuel = null;
            $secondaryFuelUsed = 0;
            $index = 1;
        }
        $tachometerBegin = $usage->getTachometerBegin();
        $distance = $usage->getDistance();
        $mainFuelUsed = $usage->getMainFuelUsed();

        $defaultFuelNorm = $auto->getDefaultFuelNorm() * (abs(7 - $date->format('m')) * 0.03 + 1);

        $distanceOnUsedFuel = round(($mainFuelUsed / $defaultFuelNorm * 100) + ($secondaryFuelUsed / $defaultFuelNorm * $index * 100), 0);

        $fuelOnDistance = round($defaultFuelNorm * $index * $distance / 100, 0);

        return ['distance' => $distanceOnUsedFuel, 'fuel' => $fuelOnDistance, 'norm' => $defaultFuelNorm];
    }

    public function updateMonth(int $transportID, string $tachometer, int $mainFuel, ?int $secondaryFuel)
    {
        $date = new DateInterval();
        $this->setTransport($transportID);
        $this->setMonthlyUsage($date->getBegin());
        $this->setMonthTachometerEnd($tachometer);
        $this->monthlyUsage->calculateUsage($mainFuel, $secondaryFuel);

        $this->monthlyUsageRepository->save($this->monthlyUsage);
    }

    public function updateMonthBegin(int $transportID, string $tachometer, int $mainFuel, ?int $secondaryFuel)
    {
        $date = new DateInterval();
        $this->setTransport($transportID);
        $this->setMonthlyUsage($date->getBegin());
        $this->setMonthTachometerEnd($tachometer);
        $this->monthlyUsage->updateMonthBegin([$tachometer, $mainFuel, $secondaryFuel]);

        $this->monthlyUsageRepository->save($this->monthlyUsage);
    }

    public function checkPreviousUsage(MonthlyUsage $monthlyUsage): MonthlyUsage
    {
        $previousDate = new DateTime($monthlyUsage->getDate()->format('Y-m-d'));
        $previousDate->modify('-1 month');
        $previousMonthUsage = $this->isMonthlyUsage($previousDate, $monthlyUsage->getTransport());
        if (!is_null($previousMonthUsage)) {
            $monthlyUsage->updateMonthBegin($previousMonthUsage->getEndSummary());
        }

        return $monthlyUsage;
    }
}
