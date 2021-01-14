<?php

declare(strict_types=1);

namespace App\Models;

use DateTime;

class ObjectDetails
{
    private $workedHours;
    private $reservedMaterials;
    private $debitedMaterials;
    private $services;
    private $income;
    private $profit;

    private $manager;
    private $foremen;
    private $staff;

    public function __construct()
    {
        $this->staff = [];
        $this->workedHours = 0;
        $this->debitedMaterials = 0;
        $this->reservedMaterials = 0;
        $this->services = 0;
        $this->income = 0;
        $this->profit = 0;
    }

    public function __toString()
    {
        return 'details';
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }

    public function getManager(): ?Worker
    {
        return $this->manager;
    }

    public function getForemen(): ?Worker
    {
        return $this->foremen;
    }

    public function getStaff(): ?array
    {
        return $this->staff;
    }

    public function getWorkedHours(): ?int
    {
        return $this->workedHours;
    }

    public function getReservedMaterials(): ?string
    {
        return (string) $this->reservedMaterials;
    }

    public function addWorkedHours(int $hours)
    {
        if (is_int($this->workedHours)) {
            $this->workedHours += $hours;
        } else {
            $this->workedHours = $hours;
        }
    }

    public function removeWorkedHours(int $hours)
    {
        $this->workedHours -= $hours;
    }

    public function addReservedMaterials(MoneyClass $sum)
    {
        if (is_null($this->reservedMaterials)) {
            $this->reservedMaterials = new MoneyClass('0');
        }
        $this->reservedMaterials->addValue($sum->getValue());
    }

    public function setManager(Worker $manager): self
    {
        $this->manager = $manager;

        return $this;
    }

    public function setForemen(Worker $foremen): self
    {
        $this->foremen = $foremen;

        return $this;
    }

    public function nullStaff()
    {
        $this->staff = [];

        return $this;
    }

    public function setStaff(Worker $worker): self
    {
        $add = true;
        foreach ($this->staff as $staff) {
            if ($staff->getStaffId() === $worker->getStaffId()) {
                $add = false;

                break;
            }
        }
        if ($add) {
            $this->staff[] = $worker;
        }

        return $this;
    }

    public function setWorkedStaff(int $staffId, int $hours, string $data): self
    {
        foreach ($this->staff as $staff) {
            if ($staff->getStaffId() === $staffId) {
                $staff->setWorkedDay(new DateTime($data), $hours);

                break;
            }
        }
        $this->calculateHours();

        return $this;
    }

    public function calculateHours(): self
    {
        $this->workedHours = 0;
        foreach ($this->staff as $staff) {
            $this->workedHours += $staff->getHours();
        }

        return $this;
    }

    /**
     * Get the value of profit.
     */
    public function getProfit()
    {
        return $this->profit;
    }

    /**
     * Set the value of profit.
     *
     * @param mixed $profit
     *
     * @return self
     */
    public function setProfit(string $profit)
    {
        $this->profit = $profit;

        return $this;
    }

    /**
     * Get the value of debitedMaterials.
     */
    public function getDebitedMaterials()
    {
        return $this->debitedMaterials;
    }

    /**
     * Set the value of debitedMaterials.
     *
     * @param mixed $debitedMaterials
     *
     * @return self
     */
    public function setDebitedMaterials(?string $debitedMaterials)
    {
        $this->debitedMaterials = $debitedMaterials;

        return $this;
    }

    /**
     * Get the value of income.
     */
    public function getIncome()
    {
        return $this->income;
    }

    /**
     * Set the value of income.
     *
     * @param mixed $income
     *
     * @return self
     */
    public function setIncome(?string $income)
    {
        $this->income = $income;

        return $this;
    }

    /**
     * Get the value of services.
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * Set the value of services.
     *
     * @param mixed $services
     *
     * @return self
     */
    public function setServices(?string $services)
    {
        $this->services = $services;

        return $this;
    }

    public function updateReservedMaterials(string $update)
    {
        $this->reservedMaterials += $update;
        $this->decreaseProfit($update);

        return $this;
    }

    public function updateDebitedMaterials(string $update)
    {
        $this->debitedMaterials += $update;
        $this->decreaseProfit($update);

        return $this;
    }

    public function updateServices(string $update)
    {
        $this->services += $update;
        $this->decreaseProfit($update);

        return $this;
    }

    public function updateIncome(string $update)
    {
        $this->income += $update;
        $this->increaseProfit($update);

        return $this;
    }

    /**
     * Set the value of reservedMaterials.
     *
     * @param mixed $reservedMaterials
     *
     * @return self
     */
    public function setReservedMaterials(?string $reservedMaterials)
    {
        $this->reservedMaterials = $reservedMaterials;

        return $this;
    }

    public function setZero()
    {
        $this->reservedMaterials = null;
        $this->debitedMaterials = null;
        $this->services = null;
        $this->income = null;
        $this->profit = null;
    }

    public function recalculateProfit()
    {
        $this->profit = $this->income - $this->reservedMaterials - $this->debitedMaterials - $this->services;

        return $this;
    }

    public function getWorkHoursByMonth(): ?array
    {
        $result = [];
        $month = [];

        foreach ($this->staff as $staff) {
            $mth = [];
            foreach ($staff->getWorkedDay() as $index => $day) {
                $m = explode('-', $index);

                if (\array_key_exists($m[0].'-'.$m[1], $mth)) {
                    $mth[$m[0].'-'.$m[1]] += $day;
                } else {
                    $mth[$m[0].'-'.$m[1]] = $day;
                }
                if (!\in_array($m[0].'-'.$m[1], $month)) {
                    $month[] = $m[0].'-'.$m[1];
                    // \var_dump($month);
                }
            }
            $result[$staff->getName()] = $mth;
        }
        sort($month);
        $result['month'] = $month;

        return $result;
    }

    private function increaseProfit(string $update)
    {
        $this->profit += $update;

        return $this;
    }

    private function decreaseProfit(string $update)
    {
        $this->profit -= $update;

        return $this;
    }
}
