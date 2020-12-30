<?php

namespace App\Form;

use App\Entity\Sales\BuhInvoiceContent;
use App\Entity\Sales\BuhInvoices;
use App\Entity\Materials\WareMaterials;
use App\Entity\Objects\WareObjects;
use App\Repository\WareMaterialsRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObjectInvoiceContentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('material', EntityType::class, [
                'class' => WareMaterials::class,
                'query_builder' => function (WareMaterialsRepository $er) {
                    return $er->createQueryBuilder('wm')
                        ->join('wm.category', 'c')
                        ->andWhere('c.type = :type')
                        ->setParameter('type', 30)
                    ;
                },
            ])
            ->add('amount', TextType::class)
            ->add('price', TextType::class)
            ->add('notes', TextareaType::class, [
                'required' => false,
            ])
            ->add('invoice', EntityType::class, [
                'class' => BuhInvoices::class,
                'disabled' => true,
            ])
            ->add('object', EntityType::class, [
                'class' => WareObjects::class,
                'disabled' => true,
                'choice_label' => 'header',
            ])
            ->add('depend')
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BuhInvoiceContent::class,
        ]);
    }
}
