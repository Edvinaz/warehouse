<?php

namespace App\Controller\Admin;

use App\Entity\Contrahents;
use App\Entity\Materials\WareMaterialCategories;
use App\Entity\Materials\WareMaterials;
use App\Entity\Objects\WareObjects;
use App\Entity\Purchases\WareInvoices;
use App\Entity\Sales\BuhContracts;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainDashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="easyadmin")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Sandelys');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section('Contrahents');
        yield MenuItem::linkToCrud('Contrahents', 'fa fa-address-card-o fa-fw', Contrahents::class);
        yield MenuItem::section('Objects');
        yield MenuItem::linkToCrud('Objects', 'fas fa-list', WareObjects::class);
        yield MenuItem::linkToCrud('Contracts', 'fas fa-list', BuhContracts::class);

        yield MenuItem::section('Purchases');
        yield MenuItem::linkToCrud('Invoices', 'fas fa-list', WareInvoices::class);

        yield MenuItem::section('Materials');
        yield MenuItem::linkToCrud('Materials', 'fas fa-list', WareMaterials::class);
        yield MenuItem::linkToCrud('Categories', 'fas fa-list', WareMaterialCategories::class);

        yield MenuItem::section();
        yield MenuItem::linkToCrud('Users', 'fas fa-users', User::class);

        yield MenuItem::linktoRoute('Home', 'fa fa-home', 'purchase');


    }

    public function configureCrud(): Crud
    {
        return Crud::new()
            ->setPaginatorPageSize(25);
    }
}
