<?php

namespace App\Entity\Transport;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FuelReceived2019Repository")
 */
class FuelReceived2019
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Transport\Transport")
     */
    private $transport;

    /**
     * @ORM\Column(type="integer")
     */
    private $fuel;

    /**
     * @ORM\Column(type="integer")
     */
    private $odometer;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $quantity;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $likuciai;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $normos;

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

    public function getFuel(): ?int
    {
        return $this->fuel;
    }

    public function setFuel(int $fuel): self
    {
        $this->fuel = $fuel;

        return $this;
    }

    public function getOdometer(): ?int
    {
        return $this->odometer;
    }

    public function setOdometer(int $odometer): self
    {
        $this->odometer = $odometer;

        return $this;
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(string $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
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

    public function getLikuciai(): ?string
    {
        return $this->likuciai;
    }

    public function setLikuciai(string $likuciai): self
    {
        $this->likuciai = $likuciai;

        return $this;
    }

    public function getNormos(): ?string
    {
        return $this->normos;
    }

    public function setNormos(string $normos): self
    {
        $this->normos = $normos;

        return $this;
    }

}
