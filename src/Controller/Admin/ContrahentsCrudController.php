<?php

namespace App\Controller\Admin;

use App\Entity\Contrahents;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ContrahentsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contrahents::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Contrahent lit')
            ->setPaginatorPageSize(15);
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
