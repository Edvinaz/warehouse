<?php

namespace App\Controller;

use DateTime;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Company;
use App\Helpers\DateInterval;
use App\Services\StatisticService;
use App\Objects\ObjectDetailsService;
use App\Services\Sales\InvoiceService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\TimeCard\TimeCardSummaryService;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DocumentPrintController extends AbstractController
{
    protected $session;
    protected $invoice;
    protected $object;

    public function __construct(
        InvoiceService $invoiceService,
        ObjectDetailsService $object
    ) {
        $this->session = new Session();
        $this->invoice = $invoiceService;
        $this->object = $object;
    }

    /**
     * @Route("/object/print/invoice/{id}", name="print_invoice")
     */
    public function printInvoice(int $id)
    {
        try {
            $this->denyAccessUnlessGranted(['ROLE_FOREMEN', 'ROLE_ADMIN']);
        } catch (AccessDeniedException $e) {
            return $this->redirectToRoute('home', [
                'alert' => 'You don\'t have right\'s, ask the administrator',
            ]);
        }
        $pdfOption = new Options();
        $pdfOption->set('defaultFont', 'timesnewroman');
        // $pdfOption->set('isHtml5ParserEnabled');
        $pdfOption->setIsRemoteEnabled(true);

        $invoice = $this->invoice->printInvoice($id);

        $domPdf = new Dompdf($pdfOption);

        $html = $this->renderView('document_print/invoice.html.twig', [
            'invoice' => $invoice,
            'company' => new Company(),
        ]);
        $domPdf->loadHtml($html);
        $domPdf->setPaper('A4', 'portrait');
        $domPdf->render();
        $domPdf->stream('REL '.$invoice->getNumber().'.pdf', [
            'Attachment' => false,
        ]);

        return new Response('The PDF file has been successfully generated !');
    }

    /**
     * @Route("/objects/print/contract/{id}", name="print_contract")
     */
    public function printContract(int $id)
    {
        try {
            $this->denyAccessUnlessGranted(['ROLE_FOREMEN', 'ROLE_ADMIN']);
        } catch (AccessDeniedException $e) {
            return $this->redirectToRoute('home');
        }
        $pdfOption = new Options();
        $pdfOption->set('defaultFont', 'timesnewroman');

        $domPdf = new Dompdf($pdfOption);

        $this->object->setObject($id);

        $html = $this->renderView('document_print/constructionContract.html.twig', [
            'object' => $this->object->getObject(),
            'company' => new Company(),
        ]);
        $domPdf->loadHtml($html);
        $domPdf->setPaper('A4', 'portrait');
        $domPdf->render();
        $domPdf->stream('sutartis '.$this->object->getObject()->getBuhContracts()->getNumber().'.pdf', [
            'Attachment' => false,
        ]);

        return new Response('The PDF file has been successfully generated !');
    }

    /**
     * @Route("/objects/print/reserved/{id}", name="print_reserved_materials")
     */
    public function printReservedMaterials(int $id, ObjectDetailsService $service)
    {
        try {
            $this->denyAccessUnlessGranted(['ROLE_FOREMEN', 'ROLE_ADMIN']);
        } catch (AccessDeniedException $e) {
            return $this->redirectToRoute('home');
        }
        $service->setObject($id);
        $workers = $service->getObject()->getEntity()->getWorkHoursByMonth();

        $month = $workers['month'];
        unset($workers['month']);

        $pdfOption = new Options();
        $pdfOption->set('defaultFont', 'timesnewroman');

        $domPdf = new Dompdf($pdfOption);
        $service->setReservedMaterials();

        $html = $this->renderView('document_print/reserved_materials.html.twig', [
            'object' => $service,
            'workers' => $workers,
            'months' => $month,
        ]);
        $domPdf->loadHtml($html);
        $domPdf->setPaper('A4', 'portrait');
        $domPdf->render();
        $domPdf->stream('rezervuota.pdf', [
            'Attachment' => false,
        ]);

        return new Response('The PDF file has been successfully generated !');
    }

    /**
     * @Route("/objects/print/reserved/{id}/{month}", name="print_reserved_materials_month")
     */
    public function printReservedMaterialsMonth(int $id, string $month, ObjectDetailsService $service)
    {
        try {
            $this->denyAccessUnlessGranted(['ROLE_FOREMEN', 'ROLE_ADMIN']);
        } catch (AccessDeniedException $e) {
            return $this->redirectToRoute('home');
        }

        $date = new DateTime(str_replace('.', '-', $month));

        $service->setObject($id);
        $service->setMonthReservedMaterials($date);

        $pdfOption = new Options();
        $pdfOption->set('defaultFont', 'timesnewroman');

        $domPdf = new Dompdf($pdfOption);

        $html = $this->renderView('document_print/reserved_materials.html.twig', [
            'object' => $service,
            'workers' => [],
            'months' => [],
            'month' => $date->format('Y-m'),
        ]);

        $domPdf->loadHtml($html);
        $domPdf->setPaper('A4', 'portrait');
        $domPdf->render();
        $domPdf->stream('rezervuota.pdf', [
            'Attachment' => false,
        ]);

        return new Response('The PDF file has been successfully generated !');
    }

    /**
     * @Route("/statistic", name="statistic")
     */
    public function statistic(StatisticService $statisticService)
    {
        try {
            $this->denyAccessUnlessGranted(['ROLE_FOREMEN', 'ROLE_ADMIN']);
        } catch (AccessDeniedException $e) {
            return $this->redirectToRoute('home');
        }
        $pdfOption = new Options();
        $pdfOption->set('defaultFont', 'timesnewroman');

        $domPdf = new Dompdf($pdfOption);

        $html = $this->renderView('document_print/monthReport.html.twig', [
            'materials' => $statisticService->getMonthReport(),
            'statistic' => [
                'month' => '2020 m. kovo mėn.',
                'begin' => 123123.12,
                'purchased' => 123123.12,
                'debited' => 123123.12,
                'end' => 123123.12,
            ],
        ]);
        $domPdf->loadHtml($html);
        $domPdf->setPaper('A4', 'landscape');
        $domPdf->render();
        $domPdf->stream('rezervuota.pdf', [
            'Attachment' => false,
        ]);

        return new Response('The PDF file has been successfully generated !');
    }

    /**
     * @Route("/print/timeCard", name="print_time_card_summary")
     */
    public function printTimeCardSummary(TimeCardSummaryService $service)
    {
        try {
            $this->denyAccessUnlessGranted(['ROLE_FOREMEN', 'ROLE_ADMIN']);
        } catch (AccessDeniedException $e) {
            return $this->redirectToRoute('home');
        }
        $date = new DateInterval();

        $pdfOption = new Options();
        $pdfOption->set('defaultFont', 'timesnewroman');

        $domPdf = new Dompdf($pdfOption);

        $html = $this->renderView('document_print/timeCardSummary.html.twig', [
            'list' => $service->getList(),
            'days' => $date->getDays(),
            'company' => new Company(),
            'date' => $date,
        ]);
        $domPdf->loadHtml($html);
        $domPdf->setPaper('A4', 'landscape');
        $domPdf->render();
        $domPdf->stream('timeCard.pdf', [
            'Attachment' => false,
        ]);
        return new Response('The PDF file has been successfully generated !');
    }
}
