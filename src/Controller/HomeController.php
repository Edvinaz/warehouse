<?php

namespace App\Controller;

use App\Services\Purchase\InvoiceListService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     * @IsGranted("ROLE_ADMIN", statusCode=418, message="Suck")
     */
    public function index(
        Request $request,
        InvoiceListService $service
    ): Response {
        // $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'User tried to access a page without having ROLE_ADMIN');

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);

        // $search = $request->get('search') ? $request->get('search') : '';
        // $purchases = $service->getList($search, (int) $request->get('page'));

        // return $this->render('purchase/index.html.twig', [
        //     'purchases' => $purchases->current(),
        //     'search' => $request->get('search'),
        //     'page' => $purchases->key(),
        //     'pages' => $purchases->count(),
        // ]);
    }

    //! from ObjectsController.php
    /**
     * @Route("/objects_test/{objectId}", name="recalculate_object_details")
     *
     * @return Response
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function objectTest(Request $request, ObjectExaminationService $service, int $objectId): Response
    {
        // TODO 
        try {
            $this->denyAccessUnlessGranted(['ROLE_ACCOUNTANT', 'ROLE_FOREMEN', 'ROLE_ADMIN']);
        } catch (AccessDeniedException $e) {
            return $this->redirectToRoute('home');
        }
        //! reikia testuoti objektus, perskaičiuoti medžiagas, pajamas ir kt.

        $service->recalculateObject($objectId);
        
        return $this->redirectToRoute('object_info', [
            'id' => $objectId,
        ]);
    } 

    //! from PurchaseController.php
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

    //! from PurchaseController.php
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
}
