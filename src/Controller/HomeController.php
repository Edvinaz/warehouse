<?php

namespace App\Controller;

use DateTime;
use App\Entity\Staff\People;
use App\Services\StatisticService;
use App\Entity\Staff\MedicalDetails;
use App\Services\Staff\StaffService;
use App\Entity\Staff\TrainingDetails;
use App\Services\Purchase\InvoiceListService;
use App\Services\Transport\FuelImportService;
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
        return $this->render('purchase/statistics.html.twig', [
            // 'report' => $service->purchaseReport(),
        ]);
    }

    //! working with people details
    /**
     * @Route("/peopleDetails", name="people_details_list")
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function PeopleDetailsList(StaffService $service)
    {
        $people = $this->getDoctrine()->getRepository(People::class)->find(1);
        $medical = new TrainingDetails();
        $medical->setPeople($people);
        $medical->setDate(new DateTime('2021-01-11'));
        // $service->savePeopleDetail($medical);
        dd($medical);


        return $this->render('purchase/statistics.html.twig', [
            'report' => [],
        ]);
    }

    /**
     * @Route("/base2", name="month_purchase_statistic")
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function BaseTwo()
    {
        return $this->render('confirm2.html.twig', [
            // 'report' => $service->purchaseReport(),
        ]);
    }

}
