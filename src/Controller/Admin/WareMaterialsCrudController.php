<?php

namespace App\Controller\Admin;

use App\Entity\Materials\WareMaterials;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class WareMaterialsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return WareMaterials::class;
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
