<?php

namespace App\Entity\Objects;

use App\Entity\Staff\Staff;
use App\Models\TimeCardDay;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TimeCardRepository")
 */
class TimeCard
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Objects\WareObjects", inversedBy="timeCards")
     */
    private $object;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Staff\Staff", inversedBy="timeCards")
     * @ORM\JoinColumn(nullable=false)
     */
    private $staff;

    /**
     * @ORM\Column(type="integer")
     */
    private $hours;

    /**
     * @ORM\Column(type="boolean")
     */
    private $wacation;

    /**
     * @ORM\Column(type="boolean")
     */
    private $free_wacation;

    /**
     * @ORM\Column(type="boolean")
     */
    private $disease;

    /**
     * @ORM\Column(type="boolean")
     */
    private $truancy;

    /**
     * @ORM\Column(type="text")
     */
    private $entity;

    public function __toString()
    {
        return 'timeCard';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getObject(): ?WareObjects
    {
        return $this->object;
    }

    public function setObject(?WareObjects $object): self
    {
        $this->object = $object;

        return $this;
    }

    public function getStaff(): ?Staff
    {
        return $this->staff;
    }

    public function setStaff(?Staff $staff): self
    {
        $this->staff = $staff;

        return $this;
    }

    public function getHours(): ?int
    {
        return $this->hours;
    }

    public function setHours(int $hours): self
    {
        $this->hours = $hours;

        return $this;
    }

    public function addHours(int $hours): self
    {
        $this->hours += $hours;
        return $this;
    }

    public function cutHours(int $hours): self
    {
        $this->hours -= $hours;
        return $this;
    }

    public function getWacation(): ?bool
    {
        return $this->wacation;
    }

    public function setWacation(bool $wacation): self
    {
        $this->wacation = $wacation;

        return $this;
    }

    public function getFreeWacation(): ?bool
    {
        return $this->free_wacation;
    }

    public function setFreeWacation(bool $free_wacation): self
    {
        $this->free_wacation = $free_wacation;

        return $this;
    }

    public function getDisease(): ?bool
    {
        return $this->disease;
    }

    public function setDisease(bool $disease): self
    {
        $this->disease = $disease;

        return $this;
    }

    public function getTruancy(): ?bool
    {
        return $this->truancy;
    }

    public function setTruancy(bool $truancy): self
    {
        $this->truancy = $truancy;

        return $this;
    }

    public function getEntity(): ?TimeCardDay
    {
        return unserialize($this->entity);
    }

    public function setEntity(TimeCardDay $entity): self
    {
        $this->entity = serialize($entity);

        return $this;
    }
}
