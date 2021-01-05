<?php

declare(strict_types=1);

namespace App\Services;

use App\Repository\WareMaterialsRepository;

class MaterialService
{
    protected $materialRepository;

    protected $list;

    public function __construct(
        WareMaterialsRepository $materialRepository
    ) {
        $this->materialRepository = $materialRepository;
        $this->list = $this->materialRepository->findAll();
    }

    public function getList()
    {
        return $this->list;
    }

    public function getListCategory(int $id = 1)
    {
        $list = [];
        foreach ($this->list as $material) {
            if ($material->getCategory()->getType() === $id) {
                $list[] = $material;
            }
        }

        return $list;
    }

    public function getMaterial(int $id)
    {
        return $this->materialRepository->find($id);
    }
}
