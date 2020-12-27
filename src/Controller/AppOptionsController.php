<?php

declare(strict_types=1);

namespace App\Controller;

use App\Models\AppOptions;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AppOptionsController extends AbstractController
{
    protected $options;

    public function __construct(
        AppOptions $options
    ) {
        $this->options = $options;
    }

    /**
     * @Route("/setPurchaseInvoicePage/{page}", name="set_purchase_invoice_page")
     *
     * @return Response
     */
    public function setPurchaseInvoicePage(int $page, Request $request): Response
    {
        try {
            $this->denyAccessUnlessGranted(['ROLE_FOREMEN', 'ROLE_ADMIN']);
        } catch (AccessDeniedException $e) {
            return $this->redirectToRoute('home');
        }
        $this->options->setPurchaseInvoiceCurrentPage($page);

        return $this->redirect($request->server->get('HTTP_REFERER'));
    }
}
