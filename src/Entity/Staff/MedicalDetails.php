<?php
namespace App\Entity\Staff;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class MedicalDetails extends PeopleDetails
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $result;

    public function getResult(): ?string
    {
        return $this->result;
    }

    public function setResult(string $result): self
    {
        $this->result = $result;

        return $this;
    }
}