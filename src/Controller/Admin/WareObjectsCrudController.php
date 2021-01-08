<?php

namespace App\Controller\Admin;

use App\Entity\Objects\WareObjects;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;

class WareObjectsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return WareObjects::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('contrahent'),
        ];
    }
    
}
