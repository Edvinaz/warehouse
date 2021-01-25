<?php
declare(strict_types=1);

namespace App\Entity\Objects;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class InstallationObject extends WareObjects
{
    public function getObjectContract()
    {
        return 'constructionContract';
    }
}
