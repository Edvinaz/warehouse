<?php

namespace App\Entity\Sales;

use App\Helpers\ToWordsLT;
use App\Entity\Contrahents;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Sales\BuhInvoiceContent;
use App\Entity\Objects\WareObjects;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BuhInvoicesRepository")
 */
class BuhInvoices
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $number;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Contrahents", inversedBy="buhInvoices")
     */
    private $contrahent;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Objects\WareObjects", inversedBy="buhInvoices")
     */
    private $object;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $note;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="boolean")
     */
    private $reverseVat;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sales\BuhInvoiceContent", mappedBy="invoice", orphanRemoval=true)
     */
    private $buhInvoiceContents;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=2)
     */
    private $total;

    public function __construct()
    {
        $this->buhInvoiceContents = new ArrayCollection();
    }

    public function __toString()
    {
        return (string) $this->number;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getContrahent(): ?Contrahents
    {
        return $this->contrahent;
    }

    public function setContrahent(?Contrahents $contrahent): self
    {
        $this->contrahent = $contrahent;

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

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

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

    public function getReverseVat(): ?bool
    {
        return $this->reverseVat;
    }

    public function setReverseVat(bool $reverseVat): self
    {
        $this->reverseVat = $reverseVat;

        return $this;
    }

    /**
     * @return Collection|BuhInvoiceContent[]
     */
    public function getBuhInvoiceContents(): Collection
    {
        return $this->buhInvoiceContents;
    }

    public function addBuhInvoiceContent(BuhInvoiceContent $buhInvoiceContent): self
    {
        if (!$this->buhInvoiceContents->contains($buhInvoiceContent)) {
            $this->buhInvoiceContents[] = $buhInvoiceContent;
            $buhInvoiceContent->setInvoice($this);
        }

        return $this;
    }

    public function removeBuhInvoiceContent(BuhInvoiceContent $buhInvoiceContent): self
    {
        if ($this->buhInvoiceContents->contains($buhInvoiceContent)) {
            $this->buhInvoiceContents->removeElement($buhInvoiceContent);
            // set the owning side to null (unless already changed)
            if ($buhInvoiceContent->getInvoice() === $this) {
                $buhInvoiceContent->setInvoice(null);
            }
        }

        return $this;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(string $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function addTotal(string $total): self
    {
        $this->total += $total;

        return $this;
    }

    public function cutTotal(string $total): self
    {
        $this->total -= $total;

        return $this;
    }

    public function month(): string
    {
        $word = new ToWordsLT();

        return $word->monthLT($this->date->format('m'));
    }

    public function totalWords(): string
    {
        $word = new ToWordsLT();
        $total = explode('.', $this->total);

        return $word->toWordsLT($this->total).' Eur '.$total[1].' ct';
    }

    public function totalVatWords(): string
    {
        $word = new ToWordsLT();
        $totalVat = strval(number_format($this->total * 1.21, 2, '.', ''));

        $total = explode('.', $totalVat);

        return $word->toWordsLT($total[0]).' Eur '.$total[1].' ct';
    }
}
