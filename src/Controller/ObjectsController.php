<?php

declare(strict_types=1);

namespace App\Controller;

use App\Objects\ObjectList;
use App\Form\WareObjectType;
use App\Form\ObjectInvoiceType;
use App\Form\ObjectContractType;
use App\Services\ObjectsService;
use App\Services\MaterialService;
use App\Entity\Objects\WareObjects;
use App\Objects\ObjectDetailsService;
use App\Form\ObjectInvoiceContentType;
use App\Interfaces\StaffListInterface;
use App\Services\Sales\InvoiceService;
use App\Services\ObjectMaterialService;
use App\Repository\WareObjectsRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\Objects\ObjectExaminationService;
use App\Repository\WareMaterialCategoriesRepository;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ObjectsController extends AbstractController
{
    protected $objectsRepository;
    protected $materialCategories;
    protected $session;
    protected $objectDetails;
    protected $objectList;
    protected $staffList;

    // public function __construct(
    //     WareObjectsRepository $objectsRepository,
    //     WareMaterialCategoriesRepository $categoriesRepository,
    //     ObjectList $objectList,                     // ObjectList service 2020-03-05
    //     ObjectDetailsService $objectDetails,         // ObjectDetails service 2020-03-05
    //     StaffListInterface $staffList
    // ) {
    //     $this->objectsRepository = $objectsRepository;
    //     $this->materialCategories = $categoriesRepository;
    //     $this->session = new Session();
    //     $this->objectDetails = $objectDetails;
    //     $this->objectList = $objectList;
    //     $this->staffList = $staffList;
    // }

    /**
     * @Route("/objects", name="objects")
     *
     * @return Response
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function index(Request $request, ObjectsService $service): Response
    {
        //! dd($_ENV['APP_SECRET']);
        // TODO reikia padaryti objektų paiešką, filtravimą pagal įvykdymą
dd($service->test());
        $list = $this->objectList->getList($request->get('search', ''), null, (int) $request->get('page', 0));
        
        return $this->render('objects/index.html.twig', [
            'object_list' => $list->current(),
            'page' => $list->key(),
            'pages' => $list->count(),
        ]);
    }

    /**
     * @Route("/objects/{id}/edit", name="edit_object")
     *
     * @return Response
     */
    public function editObject(int $id, Request $request): Response
    {
        try {
            $this->denyAccessUnlessGranted(['ROLE_FOREMEN', 'ROLE_ACCOUNTANT', 'ROLE_ADMIN']);
        } catch (AccessDeniedException $e) {
            return $this->redirectToRoute('home');
        }
        $this->objectDetails->setObject($id);

        $form = $this->createObjectForm($this->objectDetails->getObject());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $submittedObject = $form->getData();

            $submittedObject = $this->objectDetails->saveObject($submittedObject);

            return $this->redirectToRoute('object_info', [
                'id' => $submittedObject->getObject()->getId(),
            ]);
        }

        return $this->render('objects/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/objects/{id}/delete", name="delete_object")
     *
     * @return Response
     */
    public function deleteObject(int $id): Response
    {
        try {
            $this->denyAccessUnlessGranted(['ROLE_FOREMEN', 'ROLE_ACCOUNTANT', 'ROLE_ADMIN']);
        } catch (AccessDeniedException $e) {
            return $this->redirectToRoute('home');
        }
        $this->objectDetails->setObject($id);

        try {
            $this->objectDetails->deleteObject($this->objectDetails->getObject());
        } catch (AccessDeniedException $exception) {
            dd($exception);
        }

        return $this->redirectToRoute('objects');
    }

    /**
     * @Route("/objects/{id}", name="object_info")
     *
     * @return Response
     */
    public function objectInfo(int $id, Request $request, MaterialService $materials): Response
    {
        try {
            $this->denyAccessUnlessGranted(['ROLE_FOREMEN', 'ROLE_ACCOUNTANT', 'ROLE_ADMIN']);
        } catch (AccessDeniedException $e) {
            return $this->redirectToRoute('home');
        }
        $this->objectDetails->setObject($id);
        if ($request->get('staff') > 0) {
            $this->objectDetails->addObjectStaff($this->staffList->getById($request->get('staff')));
        }
        if ($request->get('manager') > 0) {
            $this->objectDetails->addObjectManager($this->staffList->getById($request->get('manager')));
        }
        if ($request->get('foremen') > 0) {
            $this->objectDetails->addObjectForemen($this->staffList->getById($request->get('foremen')));
        }

        return $this->render('objects/object.html.twig', [
            'object' => $this->objectDetails,
            'staff' => $this->staffList,
            'materials' => $materials,
        ]);
    }

    /**
     * @Route("/objects/{id}/cancel/{materialId}", name="cancel_material_reservation")
     *
     * @return Response
     */
    public function cancelMaterialReservation(
        int $id,
        int $materialId,
        Request $request,
        MaterialService $materials,
        ObjectMaterialService $objectMaterialService
    ): Response {
        try {
            $this->denyAccessUnlessGranted(['ROLE_FOREMEN', 'ROLE_ACCOUNTANT', 'ROLE_ADMIN']);
        } catch (AccessDeniedException $e) {
            return $this->redirectToRoute('home');
        }

        if ($request->get('answer')) {
            $objectMaterialService->cancelReservation($materialId, $id);
            return $this->redirectToRoute('object_info', [
                'id' => $id,
            ]);
        }

        $material = $materials->getMaterial($materialId);

        return $this->render('confirm.html.twig', [
            'message' => 'Ar tikrai norite panaikinti rezervaciją: ' . $material->getName()
        ]);
    }

    /**
     * @Route("/objects/{objectId}/contract", name="object_contract_edit")
     */
    public function objectContractEdit(int $objectId, Request $request)
    {
        try {
            $this->denyAccessUnlessGranted(['ROLE_FOREMEN', 'ROLE_ACCOUNTANT', 'ROLE_ADMIN']);
        } catch (AccessDeniedException $e) {
            return $this->redirectToRoute('home');
        }
        $this->objectDetails->setObject($objectId);

        $form = $this->createForm(ObjectContractType::class, $this->objectDetails->getContract());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->objectDetails->saveContract($form->getData());

            return $this->redirectToRoute('object_info', [
                'id' => $objectId,
            ]);
        }

        return $this->render('objects/newContract.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/objects/{id}/invoice", name="object_invoice")
     *
     * @return Response
     */
    public function objectNewInvoice(int $id, Request $request, InvoiceService $service): Response
    {
        try {
            $this->denyAccessUnlessGranted(['ROLE_FOREMEN', 'ROLE_ACCOUNTANT', 'ROLE_ADMIN']);
        } catch (AccessDeniedException $e) {
            return $this->redirectToRoute('home');
        }
        $this->objectDetails->setObject($id);

        $form = $this->createForm(ObjectInvoiceType::class, $this->objectDetails->getInvoice(null));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $service->saveInvoice($form->getData());

            return $this->redirectToRoute('object_info', [
                'id' => $id,
            ]);
        }

        return $this->render('objects/newContract.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/objects/{id}/invoice/{invoiceId}", name="object_invoice_add_content")
     *
     * @return Response
     */
    public function objectAddInvoiceContent(int $id, int $invoiceId, Request $request, InvoiceService $service): Response
    {
        try {
            $this->denyAccessUnlessGranted(['ROLE_FOREMEN', 'ROLE_ACCOUNTANT', 'ROLE_ADMIN']);
        } catch (AccessDeniedException $e) {
            return $this->redirectToRoute('home');
        }
        $this->objectDetails->setObject($id);

        $form = $this->createForm(ObjectInvoiceContentType::class, $this->objectDetails->getInvoiceContent($invoiceId));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $service->saveInvoiceContent($form->getData());

            return $this->redirectToRoute('object_info', [
                'id' => $id,
            ]);
        }

        return $this->render('objects/newContract.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/objects/{id}/remove/{contentId}", name="object_invoice_remove_content")
     *
     * @return Response
     */
    public function objectRemoveInvoiceContent(int $id, int $contentId, Request $request, InvoiceService $service): Response
    {
        try {
            $this->denyAccessUnlessGranted(['ROLE_FOREMEN', 'ROLE_ACCOUNTANT', 'ROLE_ADMIN']);
        } catch (AccessDeniedException $e) {
            return $this->redirectToRoute('home');
        }
        $this->objectDetails->setObject($id);
        $service->deleteInvoiceContent($this->objectDetails->getInvoiceContentById($contentId));

        return $this->redirectToRoute('object_info', [
            'id' => $id,
        ]);
    }

    /**
     * @Route("/objects/{id}/status/{status}", name="update_object_status")
     */
    public function updateObjectStatus(int $id, string $status, Request $request): Response
    {
        try {
            $this->denyAccessUnlessGranted(['ROLE_FOREMEN', 'ROLE_ACCOUNTANT', 'ROLE_ADMIN']);
        } catch (AccessDeniedException $e) {
            return $this->redirectToRoute('home');
        }
        $this->objectDetails->setObject($id);
        $this->objectDetails->updateStatus($status);

        return $this->redirectToRoute('object_info', [
            'id' => $id,
        ]);
    }

    /**
     * @return FormInterface
     */
    private function createObjectForm(WareObjects $object)
    {
        return $this->createForm(WareObjectType::class, $object);
    }

    /**
     * @Route("/objects_test/{objectId}", name="recalculate_object_details")
     *
     * @return Response
     */
    public function objectTest(Request $request, ObjectExaminationService $service, int $objectId): Response
    {
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
}
