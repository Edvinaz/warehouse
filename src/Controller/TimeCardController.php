<?php

namespace App\Controller;

use App\Helpers\DateInterval;
use App\Objects\ObjectDetailsService;
use App\Services\TimeCard\TimeCardService;
use App\Services\TimeCard\TimeCardSummaryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class TimeCardController extends AbstractController
{
    protected $timeCard;
    protected $object;

    public function __construct(
        TimeCardService $timeCard,
        ObjectDetailsService $object
    ) {
        $this->timeCard = $timeCard;
        $this->object = $object;
    }

    /**
     * @Route("/objects/{id}/timeCard", name="object_time_card")
     *
     * @return Response
     */
    public function objectTimeCard(int $id, Request $request): Response
    {
        try {
            $this->denyAccessUnlessGranted(['ROLE_FOREMEN', 'ROLE_ADMIN']);
        } catch (AccessDeniedException $e) {
            return $this->redirectToRoute('home');
        }
        $this->object->setObject($id);
        $this->timeCard->setStaff($this->object->getObject());

        return $this->render('time_card/index.html.twig', [
            'object' => $this->object,
        ]);
    }

    /**
     * @Route("/objects/{id}/timeCardAPI", name="object_time_card_api")
     *
     * @return Response
     */
    public function objectTimeCardApi(int $id, Request $request): Response
    {
        try {
            $this->denyAccessUnlessGranted(['ROLE_FOREMEN', 'ROLE_ADMIN']);
        } catch (AccessDeniedException $e) {
            return $this->redirectToRoute('home');
        }

        $this->object->setObject($id);
        $this->timeCard->setStaff($this->object->getObject());

        $jsonEncoder = new JsonEncoder();
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, [$jsonEncoder]);

        $jsonEvents = $serializer->serialize($this->timeCard->getList(), 'json');

        return new Response($jsonEvents, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/objects/{object}/timeCard/{staff}/{day}/{hours}",
     * name="update_object_time_card")
     *
     * @return Response
     */
    public function updateObjectTimeCard(
        int $object,
        int $staff,
        int $day,
        int $hours,
        Request $request
    ): Response {
        try {
            $this->denyAccessUnlessGranted(['ROLE_FOREMEN', 'ROLE_ADMIN']);
        } catch (AccessDeniedException $e) {
            return $this->redirectToRoute('home');
        }
        $this->object->setObject($object);
        $this->timeCard->setStaff($this->object->getObject());

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
     */
    public function timeCardSummary(TimeCardSummaryService $service)
    {
        $date = new DateInterval();
        return $this->render('time_card/summary.html.twig', [
            'list' => $service->getList(),
            'days' => $date->getDays(),
        ]);
    }

    // TODO fronte galima pasirinkti kiek valandų darbuotojas dirbo konkrečiam objekte

    // TODO iš fronto gauti duomenis apie darbuotojų ligą, atostogas ir kita.
}
