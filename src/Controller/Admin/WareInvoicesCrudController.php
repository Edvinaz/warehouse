<?php

namespace App\Controller\Admin;

use App\Entity\Purchases\WareInvoices;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class WareInvoicesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return WareInvoices::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
