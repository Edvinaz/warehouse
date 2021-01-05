<?php
declare(strict_types=1);

namespace App\Interfaces;

use DateTime;

interface WorkDayInterface
{
    public function setDate(DateTime $date): self ;

    public function getDate(): DateTime ;

    public function addHours(int $hour): self ;

    public function cutHours(int $hour): self ;

    public function getHours(): int ;

}