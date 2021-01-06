<?php

namespace App\Controller;

use App\Services\Analytics\ObjectsService;
use App\Services\Analytics\PurchaseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class AnalyticsController extends AbstractController
{
    /**
     * @Route("/analytics", name="analytics")
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(
        PurchaseService $service
    ): Response {
        return $this->render('analytics/index.html.twig', [
            'list' => $service->purchaseStatus(),
        ]);
    }

    /**
     * @Route("/analytics/{id}", name="analytics_id")
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    public function contrahentStatus(
        int $id, 
        PurchaseService $service
    ): Response {
        return $this->render('analytics/index.html.twig', [
            'list' => $service->contrahentPurchaseStatus($id),
        ]);
    }

    /**
     * @Route("/analytic/objects", name="analytics_objects")
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    public function objectsStatus(
        ObjectsService $service
    ): Response {
        return $this->render('analytics/objects.html.twig', [
            'list' => $service->getAll(),
        ]);
    }

    /**
     * @Route("/analytic/contrahent/{contrahentId}/objects", name="analytics_contrahent_objects")
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    public function contrahentObjectsStatus(
        ObjectsService $service, 
        int $contrahentId
    ): Response {
        return $this->render('analytics/objects.html.twig', [
            'list' => $service->getContrahentObjects($contrahentId),
        ]);
    }
}