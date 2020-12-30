<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\ImportFuelType;
use App\Form\WarePurchaseMaterialType;
use App\Services\Purchases\StatisticService;
use App\Services\Transport\FuelImportService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Purchases\WarePurchasedMaterials;
use App\Services\Purchase\PurchaseMaterialService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class PurchaseController extends AbstractController
{
    /**
     * @Route("/purchase/report/month", name="month_purchase_statistic")
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function MothPurchasStatistics(StatisticService $service)
    {
        try {
            $this->denyAccessUnlessGranted(['ROLE_ADMIN']);
        } catch (AccessDeniedException $e) {
            return $this->redirectToRoute('home');
        }

        return $this->render('purchase/statistics.html.twig', [
            'report' => $service->purchaseReport(),
        ]);
    }

    /**
     * @Route("/purchase/{id}/{material}", name="purchase_material")
     *
     * @return Response
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function purchaseMaterial(int $id, int $material, Request $request, PurchaseMaterialService $service): Response
    {
        $purchase = $service->createPurchaseMaterialFormModel($id, $material);

        if (0 === $material) {
            $edit = false;
        } else {
            $edit = true;
        }

        $form = $this->createForm(WarePurchaseMaterialType::class, $purchase, ['is_edit' => $edit]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $submittedMaterial = $form->getData();
            $service->saveNewMaterial($submittedMaterial, $material);

            return $this->redirectToRoute('invoice_details', ['id' => $id]);
        }

        return $this->render('purchase/purchase.html.twig', [
            'invoice' => $service->getInvoice($id),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/purchase/{id}/update/{material}", name="update_material")
     *
     * @param int $id #selected invoice id
     *
     * @return Response
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function updatePurchasedMaterial(int $id, int $material, Request $request, PurchaseMaterialService $service): Response
    {
        $purchase = $service->getMaterialPurchase($material);
        $form = $this->createForm(WarePurchaseMaterialType::class, $purchase);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $editMaterial = $form->getData();
            $this->addFlash('error', $service->updatePurchasedMaterial($editMaterial));

            return $this->redirectToRoute('invoice_details', ['id' => $id]);
        }

        return $this->render('purchase/purchase.html.twig', [
            'invoice' => $service->getInvoice($id),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/purchase/{id}/delete/{purchase}", name="delete_purchased_material")
     *
     * @return Response
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function deletePurchasedMaterial(int $id, int $purchase, PurchaseMaterialService $service): Response
    {
        $this->addFlash('error', $service->deletePurchasedMaterial($purchase));

        return $this->redirectToRoute('invoice_details', ['id' => $id]);
    }

    /**
     * @Route("/purchase/import/fuel", name="import_fuel")
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function importFuel(Request $request, FuelImportService $service)
    {
        try {
            $this->denyAccessUnlessGranted(['ROLE_ADMIN']);
        } catch (AccessDeniedException $e) {
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(ImportFuelType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $fp = fopen($form->getData()['file']->getPathName(), 'r');

            $service->importFuel($fp);
            $this->addFlash('error', 'OK');
        }

        return $this->render('transport/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
