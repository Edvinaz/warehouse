<?php

namespace App\Controller;

use App\Services\API\ObjectsService;
use App\Services\API\MaterialService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SistelaController extends AbstractController
{
    /**
     * @Route("/api/v01/object/{objectNumber}", name="sistela_object")
     */
    public function sistelaObject(int $objectNumber, ObjectsService $service): Response
    {
        return $this->json([
            $service->getObject($objectNumber)
        ]);
    }

    /**
     * @Route("/api/v01/object/materials/{objectId}", name="sistela_materials")
     */
    public function materialsList(int $objectId, MaterialService $service): Response
    {
        return $this->json([
            $service->getObjectReservedMaterials($objectId)
        ]);
    }
}
