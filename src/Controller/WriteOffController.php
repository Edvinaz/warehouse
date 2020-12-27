<?php

namespace App\Controller;

use App\Form\DebitMaterialType;
use App\Form\MaterialsSearchType;
use App\Helpers\MaterialsSearchClass;
use App\Models\WriteOffModel;
use App\Services\StatisticService;
use App\Services\WriteOffService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class WriteOffController extends AbstractController
{
    protected $writeOffModel;
    private $session;
    private $writeOffService;

    public function __construct(
        WriteOffService $offService,
        WriteOffModel $writeOffModel
    ) {
        $this->session = new Session();
        $this->writeOffService = $offService;
        $this->writeOffModel = $writeOffModel;
    }

    /**
     * @Route("/writeOff", name="write_off")
     */
    public function writeOffList(Request $request): Response
    {
        try {
            $this->denyAccessUnlessGranted(['ROLE_FOREMEN', 'ROLE_ADMIN']);
        } catch (AccessDeniedException $e) {
            return $this->redirectToRoute('home');
        }

        $session = new Session();
        $session->set('search', null);
        $session->set('category', null);

        return $this->render('objects/writeoff.html.twig', [
            'writeOffs' => $this->writeOffService->getWriteOffList(),
        ]);
    }

    /**
     * @Route("/writeOff/new", name="new_write_off")
     */
    public function newWriteOff(): Response
    {
        try {
            $this->denyAccessUnlessGranted(['ROLE_FOREMEN', 'ROLE_ADMIN']);
        } catch (AccessDeniedException $e) {
            return $this->redirectToRoute('home');
        }
        $this->writeOffService->newWriteOff();

        return $this->redirectToRoute('write_off');
    }

    /**
     * @Route("/writeOff/{id}", name="write_off_details")
     */
    public function writeOffDetails(int $id, Request $request, StatisticService $statisticService): Response
    {
        try {
            $this->denyAccessUnlessGranted(['ROLE_FOREMEN', 'ROLE_ADMIN']);
        } catch (AccessDeniedException $e) {
            return $this->redirectToRoute('home');
        }

        if ('change' === $request->get('reserved')) {
            $this->session->set('reserved', !$this->session->get('reserved'));
        }

        $session = new Session();

        $search = new MaterialsSearchClass();
        $search->setSearch($session->get('search'));
        $search->setCategory($session->get('category'));

        $form = $this->createForm(MaterialsSearchType::class, $search);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $class = $form->getData();
            $search = [
                'search' => $class->getSearch(),
                'category' => $class->getCategory()
            ];
            $session->set('category', $search['category']);
            $session->set('search', $search['search']);

        } else {
            $search = [
                'search' => $session->get('search'),
                'category' => $session->get('category')
            ];
        }

        $this->writeOffModel->initiate($id, $search);

        return $this->render('objects/writeoffdetails.html.twig', [
            'writeOff' => $this->writeOffModel,
            'statistic' => $statisticService->getPeriodStatistic(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/writeOff/{id}/{materialId}", name="write_off_material")
     */
    public function writeOffMaterial(
        int $id,
        int $materialId,
        Request $request
    ): Response {
        try {
            $this->denyAccessUnlessGranted(['ROLE_FOREMEN', 'ROLE_ADMIN']);
        } catch (AccessDeniedException $e) {
            return $this->redirectToRoute('home');
        }

        $this->writeOffModel->initiate($id, null);
        $debitMaterial = $this->writeOffModel->newDebitMaterial($materialId, true);

        $form = $this->createForm(DebitMaterialType::class, $debitMaterial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->writeOffModel->debitSelectedMaterial($form->getData());
            } catch (Exception $e) {
                dd($e);
                return $this->redirectToRoute('write_off_details', ['id' => $id]);
            }
            
            return $this->redirectToRoute('write_off_details', ['id' => $id]);
        }

        return $this->render('objects/writeoffmaterial.html.twig', [
            'writeOff' => $this->writeOffModel,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/writeOff/{id}/update/{material}", name="update_write_off_material")
     */
    public function updateWriteOffMaterial(
        int $id,
        int $material,
        Request $request
    ): Response {
        try {
            $this->denyAccessUnlessGranted(['ROLE_FOREMEN', 'ROLE_ADMIN']);
        } catch (AccessDeniedException $e) {
            return $this->redirectToRoute('home');
        }

        $this->writeOffModel->initiate($id, null);
        $debitMaterial = $this->writeOffModel->newDebitMaterial($material, false);
        $form = $this->createForm(DebitMaterialType::class, $debitMaterial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->writeOffModel->unDebitSelectedMaterial($form->getData());

            return $this->redirectToRoute('write_off_details', ['id' => $id]);
        }

        return $this->render('objects/writeoffmaterial.html.twig', [
            'writeOff' => $this->writeOffModel,
            'form' => $form->createView(),
        ]);
    }
}
