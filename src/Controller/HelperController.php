<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\SessionTimeIntervalType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

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
}