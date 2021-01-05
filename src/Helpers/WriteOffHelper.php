<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Entity\Debition\WareDebitedMaterials;
use App\Entity\Debition\WareWriteOffs;
use App\Repository\WareDebitedMaterialsRepository;
use App\Repository\WareWriteOffsRepository;

class WriteOffHelper
{
    protected $details;
    protected $materials;

    public function __construct(int $id, WareWriteOffsRepository $writeOffsRepository, WareDebitedMaterialsRepository $debitedMaterialsRepository)
    {
        $this->details = $writeOffsRepository->find($id);
        $this->materials = $debitedMaterialsRepository->getWriteOffMaterialList($id);
    }

    public function getMaterials(): WareDebitedMaterials
    {
        return $this->materials;
    }

    public function getDetails(): WareWriteOffs
    {
        return $this->details;
    }
}