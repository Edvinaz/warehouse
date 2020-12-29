<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\WareInvoiceType;
use App\Form\PurchaseInvoiceType;
use App\Services\Purchase\InvoiceDetailsService;
use App\Services\Purchase\InvoiceListService;
use App\Services\Purchase\InvoiceManageService;;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class PurchaseInvoiceController extends AbstractController
{
    /**
     * @Route("/purchase", name="purchase")
     *
     * @return Response
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function index(Request $request, InvoiceListService $service): Response
    {
        $search = $request->get('search') ? $request->get('search') : '';
        $purchases = $service->getList($search, (int) $request->get('page'));

        return $this->render('purchase/index.html.twig', [
            'purchases' => $purchases->current(),
            'search' => $request->get('search'),
            'page' => $purchases->key(),
            'pages' => $purchases->count(),
        ]);
    }

    /**
     * @Route("/purchase/newInvoice", name="new_invoice")
     *
     * @return Response
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function newInvoice(Request $request, InvoiceManageService $service): Response
    {
        $form = $this->createForm(WareInvoiceType::class, $service->getNewInvoice());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $invoice = $form->getData();

            $this->addFlash('error', $service->saveInvoice($invoice));

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
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function updateInvoice(int $id, Request $request, InvoiceManageService $service): Response
    {
        $form = $this->createForm(WareInvoiceType::class, $service->getInvoice($id));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $invoice = $form->getData();
            $this->addFlash('error', $service->saveInvoice($invoice));

            return $this->redirectToRoute('invoice_details', ['id' => $invoice->getId()]);
        }

        return $this->render('purchase/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/purchase/deleteInvoice/{id}", name="delete_invoice")
     *
     * @return Response
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function deleteInvoice(int $id, InvoiceManageService $service): Response
    {
        $this->addFlash('error', $service->deleteInvoice($id));

        return $this->redirectToRoute('purchase');
    }

    /**
     * @Route("/purchase/{id}", name="invoice_details")
     *
     * @return Response
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function invoiceDetails(int $id, Request $request, InvoiceDetailsService $service): Response
    {
        $form = $this->createForm(PurchaseInvoiceType::class, $service->getPurchaseInvoice());

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $service->setMaterialList($form->getData()->getMaterialSearch());
        }

        return $this->render('purchase/invoice.html.twig', [
            'invoice' => $service->getInvoice($id),
            'materials' => $service->getMaterialsList(),
            'form' => $form->createView(),
            'helper' => $service->getPurchaseInvoice(),
        ]);
    }
}
