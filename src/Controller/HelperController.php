<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\SessionTimeIntervalType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\TimeCard\TimeCardSummaryService;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HelperController extends AbstractController
{
    protected $session;

    public function __construct()
    {
        $this->session = new Session();
    }

    /**
     * @Route("/dateInterval", name="set_date_interval")
     *
     * @return Response
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
     * @Route("/apranga")
     */
    public function aprangosKodas(Request $request, TimeCardSummaryService $service)
    {
        if ($request->get('name')) {
            $row = $request->get('name').';'.$request->get('height').';'.$request->get('volume').';'.$request->get('shoeSize').';'.$request->get('jacketSize').';';
            $file = fopen('apranga.csv', 'a');
            fwrite($file, $row.PHP_EOL);
            fclose($file);
        }
        return $this->render('forms/apranga.html.twig', [
            'form' => $service->getList(),
        ]);
    }
}
