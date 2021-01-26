<?php

namespace App\Controller;

use DateTime;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Company;
use App\Helpers\DateInterval;
use App\Services\InvoiceService;
use App\Services\StatisticService;
use App\Services\Objects\ObjectContractService;
use App\Services\Objects\ObjectMaterialsService;
use App\Services\Sale\Form3Service;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Settings\Settings;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DocumentPrintController extends AbstractController
{

    /**
     * @Route("/object/print/invoice/{id}", name="print_invoice")
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function printInvoice(
        int $id,
        InvoiceService $service
    ) {

        $pdfOption = new Options();
        $pdfOption->set('defaultFont', 'timesnewroman');
        $pdfOption->setIsRemoteEnabled(true);

        $invoice = $service->printInvoice($id);

        $domPdf = new Dompdf($pdfOption);

        $html = $this->renderView('document_print/invoice.html.twig', [
            'invoice' => $invoice,
            'company' => new Company(),
        ]);
        $domPdf->loadHtml($html);
        $domPdf->setPaper('A4', 'portrait');
        $domPdf->render();
        $domPdf->stream(Settings::INVOICE_SERIES.' '.$invoice->getNumber().'.pdf', [
            'Attachment' => false,
        ]);

        return new Response('The PDF file has been successfully generated !');
    }

    /**
     * @Route("/object/print/form3/{id}", name="print_form_3")
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function printForm3(
        int $id,
        Form3Service $service
    ) {

        $pdfOption = new Options();
        $pdfOption->set('defaultFont', 'timesnewroman');
        $pdfOption->setIsRemoteEnabled(true);

        $invoice = $service->printForm3($id);

        $domPdf = new Dompdf($pdfOption);

        $html = $this->renderView('document_print/form3.html.twig', [
            'invoice' => $invoice,
            'company' => new Company(),
        ]);
        $domPdf->loadHtml($html);
        $domPdf->setPaper('A4', 'landscape');
        $domPdf->render();
        $domPdf->stream(Settings::INVOICE_SERIES.' '.$invoice->getNumber().'.pdf', [
            'Attachment' => false,
        ]);

        return new Response('The PDF file has been successfully generated !');
    }

    /**
     * @Route("/objects/print/contract/{id}", name="print_contract")
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function printContract(
        int $id,
        ObjectContractService $service
    ) {
        
        $pdfOption = new Options();
        $pdfOption->set('defaultFont', 'timesnewroman');

        $domPdf = new Dompdf($pdfOption);

        $object = $service->getObject($id);

        $html = $this->renderView('document_print/'.$object->getObjectContract().'.html.twig', [
            'object' => $object,
            'company' => new Company(),
        ]);
        $domPdf->loadHtml($html);
        $domPdf->setPaper('A4', 'portrait');
        $domPdf->render();
        $domPdf->stream('sutartis '.$service->getObject($id)->getBuhContracts()->getNumber().'.pdf', [
            'Attachment' => false,
        ]);

        return new Response('The PDF file has been successfully generated !');
    }

    /**
     * @Route("/objects/print/reserved/{id}", name="print_reserved_materials")
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function printReservedMaterials(
        int $id, 
        ObjectMaterialsService $service
    ) {
        $workers = $service->getObject($id)->getEntity()->getWorkHoursByMonth();

        $month = $workers['month'];
        unset($workers['month']);

        $pdfOption = new Options();
        $pdfOption->set('defaultFont', 'timesnewroman');

        $domPdf = new Dompdf($pdfOption);

        $html = $this->renderView('document_print/reserved_materials.html.twig', [
            'object' => $service->getObject($id),
            'reservedMaterials' => $service->getReservedMaterials($id),
            'reservedMaterialsSum' => $service->getReservedMaterialsSum($id),
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
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function printReservedMaterialsMonth(
        int $id, 
        string $month, 
        ObjectMaterialsService $service
    ) {

        $date = new DateTime(str_replace('.', '-', $month));

        $pdfOption = new Options();
        $pdfOption->set('defaultFont', 'timesnewroman');

        $domPdf = new Dompdf($pdfOption);

        $html = $this->renderView('document_print/reserved_materials.html.twig', [
            'object' => $service->getObject($id),
            'reservedMaterials' => $service->setMonthReservedMaterials($id, $date),
            'reservedMaterialsSum' => $service->getReservedMaterialsSum($id),
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
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function statistic(
        StatisticService $statisticService
    ) {

        // TODO implement statistic sums and month
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
     * 
     * @IsGranted("ROLE_ACCOUNTANT")
     */
    public function printTimeCardSummary(
        // TimeCardSummaryService $service
    ) {
        // TODO reikia perkelti time Card servisa
        $date = new DateInterval();

        $pdfOption = new Options();
        $pdfOption->set('defaultFont', 'timesnewroman');

        $domPdf = new Dompdf($pdfOption);

        $html = $this->renderView('document_print/timeCardSummary.html.twig', [
            // 'list' => $service->getList(),
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
