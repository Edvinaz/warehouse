<?php
declare(strict_types=1);

namespace App\Models;

use DateTime;

class SessionTimeInterval
{
    /**
     * @var DateTime
     */
    protected $date;

    public function __construct()
    {
        $this->date = new DateTime('now');
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date): self
    {
        $this->date = $date;

        return $this;
    }
}
