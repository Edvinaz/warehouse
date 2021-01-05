<?php

namespace App\Controller;

use App\Form\DebitMaterialType;
use App\Form\MaterialsSearchType;
use App\Helpers\MaterialsSearchClass;
use App\Models\WriteOffModel;
use App\Services\StatisticService;
use App\Services\WriteOff\WriteOffDetailsService;
use App\Services\WriteOff\WriteOffListService;
use App\Services\WriteOff\WriteOffManageService;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class WriteOffController extends AbstractController
{
    private $session;

    public function __construct()
    {
        $this->session = new Session();
    }

    /**
     * @Route("/writeOff", name="write_off")
     *
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function writeOffList(
        WriteOffListService $service
    ): Response {
        $this->session->set('search', null);
        $this->session->set('category', null);

        return $this->render('objects/writeoff.html.twig', [
            'writeOffs' => $service->getWriteOffList(),
        ]);
    }

    /**
     * @Route("/writeOff/new", name="new_write_off")
     *
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function newWriteOff(
        WriteOffManageService $service
    ): Response {
        $service->newWriteOff();

        return $this->redirectToRoute('write_off');
    }

    /**
     * @Route("/writeOff/{id}", name="write_off_details")
     *
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function writeOffDetails(
        int $id,
        Request $request,
        StatisticService $statisticService,
        WriteOffDetailsService $service
    ): Response {
        if ('change' === $request->get('reserved')) {
            $this->session->set('reserved', !$this->session->get('reserved'));
        }

        $search = new MaterialsSearchClass();
        $search->setSearch($this->session->get('search'));
        $search->setCategory($this->session->get('category'));

        $form = $this->createForm(MaterialsSearchType::class, $search);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $class = $form->getData();
            $search = [
                'search' => $class->getSearch(),
                'category' => $class->getCategory(),
            ];
            $this->session->set('category', $search['category']);
            $this->session->set('search', $search['search']);
        } else {
            $search = [
                'search' => $this->session->get('search'),
                'category' => $this->session->get('category'),
            ];
        }

        return $this->render('objects/writeoffdetails.html.twig', [
            'writeOff' => $service->getWriteOff($id),
            'debitedMaterials' => $service->getDebitedMaterials($id),
            'warehouseMaterials' => $service->getWarehouseMaterials($id, $search),
            'statistic' => $statisticService->getPeriodStatistic(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/writeOff/{id}/{materialId}", name="write_off_material")
     *
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function writeOffMaterial(
        int $id,
        int $materialId,
        Request $request,
        WriteOffDetailsService $service,
        WriteOffModel $model
    ): Response {
        $model->initiate($id, null);
        $debitMaterial = $model->newDebitMaterial($materialId, true);

        $form = $this->createForm(DebitMaterialType::class, $debitMaterial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $model->debitSelectedMaterial($form->getData());
            } catch (Exception $e) {
                dd($e);
                return $this->redirectToRoute('write_off_details', ['id' => $id]);
            }

            return $this->redirectToRoute('write_off_details', ['id' => $id]);
        }

        return $this->render('objects/writeoffmaterial.html.twig', [
            'writeOff' => $service->getWriteOff($id),
            'debitedMaterials' => $service->getDebitedMaterials($id),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/writeOff/{id}/update/{material}", name="update_write_off_material")
     *
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function updateWriteOffMaterial(
        int $id,
        int $material,
        Request $request,
        WriteOffModel $model,
        WriteOffDetailsService $service
    ): Response {
        $model->initiate($id, null);
        $debitMaterial = $model->newDebitMaterial($material, false);
        $form = $this->createForm(DebitMaterialType::class, $debitMaterial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $model->unDebitSelectedMaterial($form->getData());

            return $this->redirectToRoute('write_off_details', ['id' => $id]);
        }

        return $this->render('objects/writeoffmaterial.html.twig', [
            'writeOff' => $service->getWriteOff($id),
            'debitedMaterials' => $service->getDebitedMaterials($id),
            'form' => $form->createView(),
        ]);
    }
}
