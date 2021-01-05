<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\WarePurchaseMaterialType;
use App\Services\Purchase\PurchaseMaterialService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PurchaseController extends AbstractController
{
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
}
