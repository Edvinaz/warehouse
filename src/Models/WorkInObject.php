<?php
declare(strict_types=1);

namespace App\Models;

class WorkInObject
{
    protected $id;
    protected $name;
    protected $hours;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getHours(): int
    {
        return $this->hours;
    }

    public function setHours(int $hours): void
    {
        $this->hours = $hours;
    }
}
