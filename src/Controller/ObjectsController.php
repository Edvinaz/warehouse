<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\WareObjectType;
use App\Form\ObjectInvoiceType;
use App\Form\ObjectContractType;
use App\Services\MaterialService;
use App\Form\ObjectInvoiceContentType;
use App\Interfaces\StaffListInterface;
use App\Services\Objects\ObjectContractService;
use App\Services\Objects\ObjectListService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\Objects\ObjectInvoiceService;
use App\Services\Objects\ObjectManageService;
use App\Services\Objects\ObjectMaterialsService;
use App\Services\Objects\ObjectStaffService;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ObjectsController extends AbstractController
{

    /**
     * @Route("/objects", name="objects")
     *
     * @return Response
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function index(
        Request $request, 
        ObjectListService $service
    ): Response {
        //! Done
        // TODO reikia padaryti objektų paiešką, filtravimą pagal įvykdymą

        $list = $service->getObjectList(
            $request->get('search', ''), 
            $request->get('status', ''), 
            (int) $request->get('page', 0)
        );
        
        return $this->render('objects/index.html.twig', [
            'object_list' => $list->current(),
            'page' => $list->key(),
            'pages' => $list->count(),
            'search' => $request->get('search', '')
        ]);
    }

    /**
     * @Route("/objects/{id}/edit", name="edit_object")
     *
     * @return Response
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function editObject(
        int $id, 
        Request $request, 
        ObjectManageService $service
    ): Response {
        $form = $this->createForm(WareObjectType::class, $service->getObject($id));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $submittedObject = $form->getData();

            $submittedObject = $service->saveObject($submittedObject);

            return $this->redirectToRoute('object_info', [
                'id' => $id,
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
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function deleteObject(
        int $id, 
        ObjectManageService $service
    ): Response { 
        try {
            $service->deleteObject($service->getObject($id));
        } catch (AccessDeniedException $exception) {
            throw new Exception('Something was wrong');
        }

        return $this->redirectToRoute('objects');
    }

    /**
     * @Route("/objects/{id}", name="object_info")
     *
     * @return Response
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function objectInfo(
        int $id, 
        Request $request,  
        ObjectManageService $service,
        ObjectStaffService $staffService,
        StaffListInterface $staffList,
        ObjectMaterialsService $materials
    ): Response {
        // ! still needs to test out

        $object = $service->getObject($id);

        if ($request->get('staff') > 0) {
            $staffService->addObjectStaff($staffList->getById($request->get('staff')), $id);
        }
        if ($request->get('manager') > 0) {
            $staffService->addObjectManager($staffList->getById($request->get('manager')), $id);
        }
        if ($request->get('foremen') > 0) {
            $staffService->addObjectForemen($staffList->getById($request->get('foremen')), $id);
        }

        return $this->render('objects/object.html.twig', [
            'object' => $object,
            'staff' => $staffList,
            'reservedMaterials' => $materials->getReservedMaterials($id),
            'reservedMaterialsByMonth' => $service->calculateObjectReservedMaterialsByPurchaseMonth($id),
        ]);
    }

    /**
     * @Route("/objects/{id}/cancel/{materialId}", name="cancel_material_reservation")
     *
     * @return Response
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function cancelMaterialReservation(
        int $id,
        int $materialId,
        Request $request,
        MaterialService $materials,
        ObjectMaterialsService $objectMaterialService
    ): Response {

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
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function objectContractEdit(
        int $objectId, 
        Request $request,
        ObjectContractService $service
    ): Response {

        $form = $this->createForm(
            ObjectContractType::class, 
            $service->getContract($objectId)
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $service->saveContract($form->getData());

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
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function objectNewInvoice(
        int $id, 
        Request $request, 
        ObjectInvoiceService $service
    ): Response {

        $invoiceId = $request->get('invoiceId', null);

        $form = $this->createForm(
            ObjectInvoiceType::class, 
            $service->getInvoice($id, $invoiceId)
        );

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
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function objectAddInvoiceContent(
        int $id, 
        int $invoiceId, 
        Request $request, 
        ObjectInvoiceService $service
    ): Response {

        $form = $this->createForm(
            ObjectInvoiceContentType::class, 
            $service->getInvoiceContent($id, $invoiceId)
        );

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
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function objectRemoveInvoiceContent(
        int $id, 
        int $contentId,
        ObjectInvoiceService $service
    ): Response {

        $service->deleteInvoiceContent($contentId);

        return $this->redirectToRoute('object_info', [
            'id' => $id,
        ]);
    }

    /**
     * @Route("/objects/{id}/status/{status}", name="update_object_status")
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function updateObjectStatus(
        int $id, 
        string $status, 
        ObjectManageService $service
    ): Response {

        $service->updateStatus($id, $status);

        return $this->redirectToRoute('object_info', [
            'id' => $id,
        ]);
    } 
}
