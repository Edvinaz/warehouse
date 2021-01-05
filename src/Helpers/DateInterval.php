<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\TimeCardDay;
use DateTime;
use DateTimeInterface;
use Exception;
use Symfony\Component\HttpFoundation\Session\Session;

class DateInterval
{
    protected $begin;
    protected $end;
    private static $year;
    private static $month;
    private static $sbegin;
    private static $send;

    public function __construct()
    {
        $session = new Session();

        try {
            if (\is_null($session->get('interval'))) {
                $this->begin = new DateTime('now');
            } else {
                $this->begin = new DateTime($session->get('interval')->getDate()->format('Y-m-01'));
            }
        } catch (Exception $e) {
        }

        try {
            if (\is_null($session->get('interval'))) {
                $this->end = new DateTime('now');
            } else {
                $this->end = new DateTime($session->get('interval')->getDate()->format('Y-m-t'));
            }
        } catch (Exception $e) {
        }
        self::$sbegin = $this->begin;
        self::$send = $this->end;
        self::$year = $this->begin->format('Y');
        self::$month = $this->begin->format('m');
    }

    public function getBegin(): DateTime
    {
        return $this->begin;
    }

    public function getEnd(): DateTime
    {
        return $this->end;
    }

    public function getDay(int $day)
    {
        return $this->begin->format('Y-m-'.$day);
    }

    public function getDateIntervalDays(): array
    {
        $diff = $this->begin->diff($this->end);
        $days = [];
        for ($i = 0; $i <= $diff->format('%R%a days'); ++$i) {
            $day = new TimeCardDay($i + 1);
            $days[] = $day;
        }

        return $days;
    }

    public function getIntervalDaysList(): array
    {
        $diff = $this->begin->diff($this->end);
        $days = [];
        for ($i = 0; $i <= $diff->format('%R%a days'); ++$i) {
            $days[$i + 1] = 0;
        }

        return $days;
    }

    public function getDays(): int
    {
        return (int) $this->end->format('t');
    }

    public static function getYear(): string
    {
        return (string) self::$year;
    }

    public static function getMonth(): string
    {
        return (string) self::$month;
    }

    public static function getIntervalDays(): array
    {
        $diff = self::$send->diff(self::$sbegin);

        $days = [];

        for ($i = 0; $i <= $diff->d; ++$i) {
            $day = new TimeCardDay($i + 1);
            $days[] = $day;
        }

        return $days;
    }

    public static function getSbegin()
    {
        return self::$sbegin;
    }

    public static function getSend()
    {
        return self::$send;
    }

    public function inRange(DateTimeInterface $date): bool
    {
        $from = $this->begin->diff($date)->format('%R%a');
        $modifyedDate = new DateTime($this->end->format('Y-m-t'));
        $modifyedDate->modify('+1 month');
        $to = $modifyedDate->diff($date)->format('%R%a');

        if ($from >= 0 && $from < 62 && $to <= 0 && $to > -62) {
            return true;
        }

        return false;
    }

    public function inThisMonth(DateTimeInterface $date)
    {
        if ($date->format('Y-m') == $this->begin->format('Y-m')) {
            return true;
        }
        return false;
    }

    public function differenceToday(DateTimeInterface $date): string
    {
        $today = new DateTime();
        return $today->diff($date)->format('%R%a');
    }

    public function getMonthWords()
    {
        switch($this->getBegin()->format('m')) {
            case 1:
                return 'sausio';
            break;
            case 2:
                return 'vasario';
            break;
            case 3:
                return 'kovo';
            break;
            case 4:
                return 'balandžio';
            break;
            case 5:
                return 'gegužės';
            break;
            case 6:
                return 'birželio';
            break;
            case 7:
                return 'liepos';
            break;
            case 8:
                return 'rugpjūčio';
            break;
            case 9:
                return 'rugsėjo';
            break;
            case 10:
                return 'spalio';
            break;
            case 11:
                return 'lapkričio';
            break;
            case 12:
                return 'gruodžio';
            break;
        }
    }
}
