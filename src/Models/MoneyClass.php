<?php

declare(strict_types=1);

namespace App\Models;

use Exception;

class MoneyClass
{
    private $euros;
    private $cents;

    private $value;

    public function __construct(?string $gotValue)
    {
        $this->value = \str_replace(',', '.', $gotValue);
        $money = explode('.', $this->value);
        $this->setEuros($money[0]);
        $this->cents = $this->formatReceivedValueCents($money);
    }

    public function __toString()
    {
        return $this->euros.'.'.$this->cents;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function roundedValue(): string
    {
        $round = round(($this->cents / 1000), 0);

        return $this->euros.'.'.$round;
    }

    public function addValue(string $value): string
    {
        $money = $this->validateReceivedValue($value);
        $cents = $this->formatCents($money);
        $this->euros += $this->formatReceivedValueEuros($money);
        $this->sumCents($cents);
        $this->setValue();

        return $this->value;
    }

    public function subtractValue(string $value): string
    {
        $money = $this->validateReceivedValue($value);
        $cents = $this->formatCents($money);
        $this->euros -= $this->formatReceivedValueEuros($money);
        $this->subtractCents($cents);
        $this->setValue();

        return $this->value;
    }

    /**
     *  Below ONLY private methods of this class
     *  Default cents 5 digits, function sums current cents with given new ones.
     */
    private function sumCents(string $cents): void
    {
        $existing = str_split($this->cents);
        $subtracting = str_split($cents);
        $res = [];
        $mindNext = 0;

        for ($i = 4; $i >= 0; --$i) {
            $sum = strval(intval($existing[$i]) + intval($subtracting[$i]));
            $mind = $mindNext;

            if ($sum >= 10) {
                $sumArray = str_split(strval($sum));
                $mindNext = 1;
                $sum = $sumArray[1];
            } else {
                $mindNext = 0;
            }
            $res[] = intval($sum) + intval($mind);
        }

        $result = [];
        for ($i = 4; $i >= 0; --$i) {
            $result[] = $res[$i];
        }

        $this->euros += $mindNext;
        $this->cents = implode('', $result);
    }

    private function subtractCents(string $cents): void
    {
        $existing = str_split($this->cents);
        $subtracting = str_split($cents);
        $res = [];
        $inMind = 0;

        for ($i = 4; $i >= 0; --$i) {
            $sum = intval($existing[$i]) - intval($subtracting[$i]);
            $mind = $inMind;

            if ($sum < 0) {
                $sum += 10;
                $inMind = 1;
            } else {
                $inMind = 0;
            }
            $res[] = strval($sum - $mind);
        }

        $result = [];
        for ($i = 4; $i >= 0; --$i) {
            $result[] = $res[$i];
        }

        $this->euros -= $inMind;
        $this->cents = implode('', $result);
    }

    private function setValue(): self
    {
        $this->value = $this->euros.'.'.$this->cents;

        return $this;
    }

    private function formatCents(array $money): string
    {
        if (1 === count($money)) {
            $cents = '00000';
        } else {
            $cents = $money[1];
        }

        switch (strlen($cents)) {
            case 1:
                $cents = $cents * 10000;

                break;
            case 2:
                $cents = $cents * 1000;

                break;
            case 3:
                $cents = $cents * 100;

                break;
            case 4:
                $cents = $cents * 10;

                break;
            case 5:
                break;
            default:
                throw new Exception('MoneyClass formatCents() Exception');
        }

        return strval($cents);
    }

    private function setEuros(string $euros): self
    {
        if (\is_numeric($euros)) {
            $this->euros = $euros;
        } else {
            $this->euros = 0;
        }

        return $this;
    }

    private function setCents(string $cents): self
    {
        $this->cents = $cents;

        return $this;
    }

    private function formatReceivedValueCents(array $money): string
    {
        if (1 === count($money)) {
            return '00000';
        }

        return $money[1];
    }

    private function formatReceivedValueEuros(array $money): string
    {
        if ('' === $money[0]) {
            return '0';
        }

        return $money[0];
    }

    private function validateReceivedValue(string $value): array
    {
        if (!is_numeric($value)) {
            throw new Exception('Given value must be number');
            // return 'BAD';
        }

        $this->value += \str_replace(',', '.', $value);

        return explode('.', strval($value));
    }
}
