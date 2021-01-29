<?php
declare(strict_types=1);

namespace App\Entity\Objects;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class MeasurementObject extends WareObjects
{
    public function getObjectContract()
    {
        return 'measurementContract';
    }

    public function getObjectContractName()
    {
        return 'measurementContract';
    }
}
