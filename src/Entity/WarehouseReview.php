<?php

namespace App\Entity;

use App\Helpers\ToWordsLT;
use App\Repository\WarehouseReviewRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WarehouseReviewRepository::class)
 */
class WarehouseReview
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $month;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $begin;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $purchased;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $debited;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $end;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMonth(): ?\DateTimeInterface
    {
        return $this->month;
    }

    public function getMonthWord():string
    {
        $word = new ToWordsLT();
        return $this->month->format('Y').'m. '.$word->monthLT($this->month->format('m')).' mėn.';
    }

    public function setMonth(\DateTimeInterface $month): self
    {
        $this->month = $month;

        return $this;
    }

    public function getBegin(): ?string
    {
        return $this->begin;
    }

    public function setBegin(string $begin): self
    {
        $this->begin = $begin;

        return $this;
    }

    public function getPurchased(): ?string
    {
        return $this->purchased;
    }

    public function setPurchased(string $purchased): self
    {
        $this->purchased = $purchased;

        return $this;
    }

    public function getDebited(): ?string
    {
        return $this->debited;
    }

    public function setDebited(string $debited): self
    {
        $this->debited = $debited;

        return $this;
    }

    public function getEnd(): ?string
    {
        return $this->end;
    }

    public function setEnd(string $end): self
    {
        $this->end = $end;

        return $this;
    }
}
