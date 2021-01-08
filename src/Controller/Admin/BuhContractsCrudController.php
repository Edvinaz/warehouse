<?php

namespace App\Controller\Admin;

use App\Entity\Sales\BuhContracts;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class BuhContractsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BuhContracts::class;
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
