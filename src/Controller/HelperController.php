<?php

declare(strict_types=1);

namespace App\Controller;

use App\Models\CompanyUpdate;
use App\Form\CompanyDetailsType;
use App\Services\StatisticService;
use App\Form\SessionTimeIntervalType;
use App\Helpers\CompanyDetailsUpdater;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\Transport\TransportBlockService;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HelperController extends AbstractController
{
    protected $session;

    public function __construct()
    {
        $this->session = new Session();
    }

    /**
    * @Route("/", name="dashboard")
    */
    public function homes(TransportBlockService $transport)
    {
        return $this->render('home/index.html.twig', [
            'list' => $transport->getBlockList(),
        ]);
    }

    /**
     * @Route("/dateInterval", name="set_date_interval")
     *
     * @return Response
     * 
     * @IsGranted("ROLE_USER")
     */
    public function setDateInterval(Request $request): Response
    {
        $interval = $this->session->get('interval');

        $form = $this->createForm(SessionTimeIntervalType::class, $interval);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $dateInterval = $form->getData();

            return $this->redirect($request->server->get('HTTP_REFERER'));
        }

        return $this->render('helper/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/overview", name="overview")
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function overview(
        StatisticService $statisticService
    ) {
        return $this->render('statistics/overview.html.twig', [
            'period' => $statisticService->warehouseOverview(19),
        ]);
    }

    /**
     * @Route("/companyDetails", name="company_details")
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    public function companyDetails(Request $request) {
        $company = new CompanyUpdate();
        $form = $this->createForm(CompanyDetailsType::class, $company);

        $form->handleRequest($request);
        if($form->isSubmitted()){
            CompanyDetailsUpdater::updateDetails($form->getData());
        }
        return $this->render('form.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}
