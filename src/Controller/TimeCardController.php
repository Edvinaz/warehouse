<?php

namespace App\Controller;

use App\Helpers\DateInterval;
use App\Services\ObjectsService;
use App\Services\TimeCard\TimeCardService;
use App\Services\TimeCard\TimeCardSummaryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class TimeCardController extends AbstractController
{
    protected $timeCard;
    protected $object;

    public function __construct(
        TimeCardService $timeCard
    ) {
        $this->timeCard = $timeCard;
    }

    /**
     * @Route("/objects/{id}/timeCard", name="object_time_card")
     *
     * @return Response
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function objectTimeCard(
        int $id, 
        ObjectsService $service
    ): Response {

        return $this->render('time_card/index.html.twig', [
            'object' => $service->getObject($id),
        ]);
    }

    /**
     * @Route("/objects/{id}/timeCardAPI", name="object_time_card_api")
     *
     * @return Response
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function objectTimeCardApi(
        int $id, 
        ObjectsService $service
    ): Response {
        $this->timeCard->setStaff($service->getObject($id));

        $jsonEncoder = new JsonEncoder();
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, [$jsonEncoder]);

        $jsonEvents = $serializer->serialize($this->timeCard->getList(), 'json');

        return new Response($jsonEvents, 200, [
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     * @Route("/objects/{object}/timeCard/{staff}/{day}/{hours}",
     * name="update_object_time_card")
     *
     * @return Response
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function updateObjectTimeCard(
        int $object,
        int $staff,
        int $day,
        int $hours,
        ObjectsService $service
    ): Response {

        $this->timeCard->setStaff($service->getObject($object));

        $result = $this->timeCard->updateWorkDay($staff, $day, $hours);

        $jsonEncoder = new JsonEncoder();
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, [$jsonEncoder]);

        $jsonEvents = $serializer->serialize($result, 'json');

        return new Response($jsonEvents, 200, [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * @Route("timeCard", name="time_card_summary")
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function timeCardSummary(
        TimeCardSummaryService $service
    ) {
        $date = new DateInterval();
        return $this->render('time_card/summary.html.twig', [
            'list' => $service->getList(),
            'days' => $date->getDays(),
        ]);
    }

    // TODO fronte galima pasirinkti kiek valandų darbuotojas dirbo konkrečiam objekte

    // TODO iš fronto gauti duomenis apie darbuotojų ligą, atostogas ir kita.
}