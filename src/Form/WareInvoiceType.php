<?php

namespace App\Form;

use App\Entity\Contrahents;
use App\Entity\Purchases\WareInvoices;
use App\Entity\Objects\WareObjects;
use App\Repository\WareObjectsRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WareInvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('number', TextType::class)
            ->add('date', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('contrahent', EntityType::class, [
                'class' => Contrahents::class,
            ])
            ->add('object', EntityType::class, [
                'class' => WareObjects::class,
                'query_builder' => function (WareObjectsRepository $repository) {
                    return $repository->createQueryBuilder('wo')
                        ->andWhere('wo.status = :status')
                        ->setParameter('status', 'EXECUTING')
                    ;
                },
                'required' => false,
                'choice_label' => 'header',
            ])
            ->add('submit', SubmitType::class)
//            ->add('cancel', ResetType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WareInvoices::class,
        ]);
    }
}
