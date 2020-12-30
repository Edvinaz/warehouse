<?php

namespace App\Form;

use App\Entity\Materials\WareMaterialCategories;
use App\Helpers\MaterialsSearchClass;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MaterialsSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('search', TextType::class, [
                'required' => false,
            ])
            ->add('category', EntityType::class, [
                'class' => WareMaterialCategories::class,
                'required' => false,
                'choice_value' => function (?WareMaterialCategories $entity) {
                    return $entity;
                }
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data-class' => MaterialsSearchClass::class
        ]);
    }
}
