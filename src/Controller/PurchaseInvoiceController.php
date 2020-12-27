<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\WareInvoiceType;
use App\Form\PurchaseInvoiceType;
use App\Services\Purchases\InvoiceService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PurchaseInvoiceController extends AbstractController
{
    private $purchaseService;

    public function __construct(
        InvoiceService $purchaseService
    ) {
        $this->purchaseService = $purchaseService;
    }

    /**
     * @Route("/purchase", name="purchase")
     *
     * @return Response
     */
    public function index(Request $request): Response
    {
        try {
            $this->denyAccessUnlessGranted(['ROLE_ACCOUNTANT', 'ROLE_ADMIN']);
        } catch (AccessDeniedException $e) {
            return $this->redirectToRoute('home');
        }
        $purchases = $this->purchaseService->initiatePurchases(
            $request->get('search', ''),
            $request->get('page', '0')
        );

        return $this->render('purchase/index.html.twig', [
            'purchases' => $purchases,
        ]);
    }

    /**
     * @Route("/purchase/newInvoice", name="new_invoice")
     *
     * @return Response
     */
    public function newInvoice(Request $request): Response
    {
        try {
            $this->denyAccessUnlessGranted(['ROLE_ACCOUNTANT', 'ROLE_ADMIN']);
        } catch (AccessDeniedException $e) {
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(WareInvoiceType::class, $this->purchaseService->newInvoice());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $invoice = $form->getData();

            $this->addFlash('error', $this->purchaseService->saveInvoice($invoice));

            return $this->redirectToRoute('invoice_details', ['id' => $invoice->getId()]);
        }

        return $this->render('purchase/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/invoicepurchase/editInvoice/{id}", name="edit_invoice")
     *
     * @return Response
     */
    public function updateInvoice(int $id, Request $request): Response
    {
        try {
            $this->denyAccessUnlessGranted(['ROLE_ACCOUNTANT', 'ROLE_ADMIN']);
        } catch (AccessDeniedException $e) {
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(WareInvoiceType::class, $this->purchaseService->getInvoice($id));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $invoice = $form->getData();
            $this->addFlash('error', $this->purchaseService->saveInvoice($invoice));

            return $this->redirectToRoute('invoice_details', ['id' => $invoice->getId()]);
        }

        return $this->render('purchase/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/purchase/{id}", name="invoice_details")
     *
     * @return Response
     */
    public function invoiceDetails(int $id, Request $request): Response
    {
        try {
            $this->denyAccessUnlessGranted(['ROLE_ACCOUNTANT', 'ROLE_ADMIN']);
        } catch (AccessDeniedException $e) {
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(PurchaseInvoiceType::class, $this->purchaseService->getPurchaseInvoice());

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->purchaseService->setMaterialList($form->getData()->getMaterialSearch());
        }

        return $this->render('purchase/invoice.html.twig', [
            'invoice' => $this->purchaseService->getInvoice($id),
            'materials' => $this->purchaseService->getMaterialsList(),
            'form' => $form->createView(),
            'helper' => $this->purchaseService->getPurchaseInvoice(),
        ]);
    }
}
