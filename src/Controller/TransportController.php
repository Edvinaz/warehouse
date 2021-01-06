<?php

namespace App\Controller;

use Error;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Transport\Transport;
use App\Form\TransportType;
use App\Helpers\DateInterval;
use App\Services\FuelService;
use App\Repository\TransportRepository;
use App\Services\FuelManagementService;
use App\Services\StatisticService;
use App\Services\Transport\FuelUsageService;
use App\Services\Transport\TransportService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Services\TransportService as ServicesTransportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class TransportController extends AbstractController
{
    protected $transportService;
    protected $monthlyUsageService;
    protected $transportRepository;
    protected $fuelManager;

    public function __construct(
        TransportRepository $repository,
        TransportService $transportService,
        FuelUsageService $monthlyUsageService,
        FuelManagementService $fuelManager
    ) {
        $this->transportService = $transportService;
        $this->transportRepository = $repository;
        $this->monthlyUsageService = $monthlyUsageService;
        $this->fuelManager = $fuelManager;
    }

    /**
     * @Route("/transport", name="transport_list")
     * 
     * @IsGranted("ROLE_USER")
     */
    public function transportList(
        ServicesTransportService $service
    ) {
        return $this->render('transport/list.html.twig', [
            'list' => $service->getFullList(),
        ]);
    }

    /**
     * @Route("/transport/month", name="transport_usage_list")
     * 
     * @IsGranted("ROLE_USER")
     */
    public function transportUsageList(
        FuelService $service, 
        StatisticService $statistic
    ) {

        $date = new DateInterval();
        return $this->render('transport/listMonth.html.twig', [
            'list' => $this->fuelManager->getList(),
            'status' => $this->fuelManager->getFuelStatus(),
            'purchase' => $service->getFuelStatistic()[$date->getBegin()->format('Y-m')],
            'remaind' => $statistic->getMonthFuel()
        ]);
    }

    /**
     * @Route("/transport/month/report", name="transport_usage_report")
     * 
     * @IsGranted("ROLE_USER")
     */
    public function transportUsageReport()
    {
        $pdfOption = new Options();
        $pdfOption->set('defaultFont', 'timesnewroman');

        $domPdf = new Dompdf($pdfOption);

        $html = $this->renderView('document_print/fuel_month_report.html.twig', [
            'list' => $this->fuelManager->getList(),
        ]);
        $domPdf->loadHtml($html);
        $domPdf->setPaper('A4', 'landscape');
        $domPdf->render();
        $domPdf->stream('fuelReport.pdf', [
            'Attachment' => false,
        ]);
        return new Response('The PDF file has been successfully generated !');
    }

    /**
     * @Route("/transport/month/update", name="transport_month_update")
     * 
     * @IsGranted("ROLE_USER")
     */
    public function transportMonthUpdate(
        Request $request
    ) {
        $this->fuelManager->updateMonth(
            (int) $request->get('transport'),
            $request->get('tachometer'),
            (int) $request->get('mainRemainder'),
            (int) $request->get('secondRemainder')
        );

        return $this->redirectToRoute('transport_usage_list');
    }

    /**
    * @Route("/transport/month/update/start", name="transport_month_update_start")
    * @IsGranted("ROLE_USER")
    */
    //  transport/month/update/start?transport=1&tachometer=1&mainRemainder=1&secondRemainder=1
    public function transportMonthUpdateStart(
        Request $request
    ) {
        if (is_null($request->get('secondRemainder'))) {
            $secondary = null;
        } else {
            $secondary = (int) $request->get('secondRemainder');
        }
        // $this->fuelManager->updateMonthBegin(
        //     (int) $request->get('transport'),
        //     $request->get('tachometer'),
        //     (int) $request->get('mainRemainder'),
        //     $secondary
        // );

        return $this->redirectToRoute('transport_usage_list');
    }

    /**
     * @Route("/transport/monthlyUsage", name="create_transport_monthly_usage")
     * 
     * @IsGranted("ROLE_USER")
     */
    public function createTransportMonthlyUsage(
        Request $request
    ) {
        $session = new Session();

        try {
            $this->fuelManager->setTransport($request->get('auto'));
            $this->fuelManager->setMonthlyUsage($session->get('interval')->getdate());
        } catch (Error $e) {
            dd($e->getMessage());
        }

        return $this->redirectToRoute('transport_list');
    }

    /**
     * @Route("/transport/form/{id}", name="transport_form")
     * 
     * @IsGranted("ROLE_USER")
     */
    public function transportForm(
        Request $request, 
        int $id
    ) {
        if (0 === $id) {
            $transport = new Transport();
        } else {
            $transport = $this->getDoctrine()->getRepository(Transport::class)->find($id);
        }

        $manager = $this->getDoctrine()->getManager();

        $form = $this->createForm(TransportType::class, $transport);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $manager = $this->getDoctrine()->getManager();

            $manager->persist($form->getData());
            $manager->flush();

            return $this->redirectToRoute('transport_list');
        }

        return $this->render('transport/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/transport/fuel", name="transport_fuel")
     * 
     * @IsGranted("ROLE_USER")
     */
    public function transportFuel(
        FuelService $service, 
        ServicesTransportService $transport
    ) {
        return $this->render('transport/fuel.html.twig', [
            'list' => $service->getList(),
            'transport' => $transport->getList(),
        ]);
    }

    /**
     * @Route("/transport/fuel/statistic", name="transport_fuel_statistic")
     * 
     * @IsGranted("ROLE_USER")
     */
    public function transportFuelStatistic(
        FuelService $service
    ) {
        return $this->render('transport/fuelstatistic.html.twig', [
            'list' => $service->getFuelStatistic(),
        ]);
    }

    /**
     * @Route("/transport/fuel/{id}", name="add_fuel")
     * 
     * @IsGranted("ROLE_USER")
     */
    public function fuels(
        int $id, 
        Request $request
    ) {
        $this->fuelManager->setTransport((int) $request->get('auto'));
        $this->fuelManager->setFuel((int) $request->get('id'), $request->get('tacho'));

        return $this->redirectToRoute('transport_fuel');
    }

    /**
     * @Route("/transport/tachometer/end", name="set_tachometer_end")
     * 
     * @IsGranted("ROLE_USER")
     */
    public function setTachometerEnd(
        Request $request
    ) {
        $this->fuelManager->setTransport((int) $request->get('auto'));
        $this->fuelManager->setMonthTachometerEnd('123');
    }
}