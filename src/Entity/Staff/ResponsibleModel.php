<?php
namespace App\Entity\Staff;

use App\Entity\Staff\Staff;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class ResponsibleModel extends Staff
{

    /**
     * @ORM\Column(type="string", length=256)
     */
    private $notes;

    public function getTrueName()
    {
        return $this->getPosition().' '.$this->getFullName();;
    }

    public function getNotes()
    {
        return $this->notes;
    }

    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

}