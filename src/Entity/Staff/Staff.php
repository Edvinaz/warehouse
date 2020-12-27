<?php

namespace App\Entity\Staff;

use App\Entity\Staff\People;
use App\Entity\Objects\TimeCard;
use App\Interfaces\Staff\StaffInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StaffRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string", length=4)
 * @ORM\DiscriminatorMap({
 *      "work" = "WorkerModel",
 *      "lead" = "ResponsibleModel"
 * })
 */
abstract class Staff implements StaffInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Staff\People", inversedBy="staff")
     * @ORM\JoinColumn(nullable=false)
     */
    private $person;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $position;

    /**
     * @ORM\Column(type="boolean")
     */
    private $responsible;

    /**
     * @ORM\Column(type="date")
     */
    private $accepted;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fired;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Objects\TimeCard", mappedBy="staff")
     */
    private $timeCards;

    public function __construct()
    {
        $this->timeCards = new ArrayCollection();
    }

    public function __toString()
    {
        return (string)$this->person->getFullName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPerson(): ?People
    {
        return $this->person;
    }

    public function setPerson(?People $person): self
    {
        $this->person = $person;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getResponsible(): ?bool
    {
        return $this->responsible;
    }

    public function setResponsible(bool $responsible): self
    {
        $this->responsible = $responsible;

        return $this;
    }

    public function getAccepted(): ?\DateTimeInterface
    {
        return $this->accepted;
    }

    public function setAccepted(\DateTimeInterface $accepted): self
    {
        $this->accepted = $accepted;

        return $this;
    }

    public function getFired(): ?\DateTimeInterface
    {
        return $this->fired;
    }

    public function setFired(?\DateTimeInterface $fired): self
    {
        $this->fired = $fired;

        return $this;
    }

    /**
     * @return Collection|TimeCard[]
     */
    public function getTimeCards(): Collection
    {
        return $this->timeCards;
    }

    public function addTimeCard(TimeCard $timeCard): self
    {
        if (!$this->timeCards->contains($timeCard)) {
            $this->timeCards[] = $timeCard;
            // $timeCard->setStaff($this);
        }

        return $this;
    }

    public function removeTimeCard(TimeCard $timeCard): self
    {
        if ($this->timeCards->contains($timeCard)) {
            $this->timeCards->removeElement($timeCard);
            // set the owning side to null (unless already changed)
            if ($timeCard->getStaff() === $this) {
                $timeCard->setStaff(null);
            }
        }

        return $this;
    }

    public function getFullName()
    {
        return (string)$this->person->getFullName();
    }
}
