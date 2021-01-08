<?php

namespace App\Controller\Admin;

use App\Entity\Materials\WareMaterialCategories;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class WareMaterialCategoriesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return WareMaterialCategories::class;
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
