<?php

namespace App\Form;

use App\Entity\Sales\BuhInvoices;
use App\Entity\Contrahents;
use App\Entity\Objects\WareObjects;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObjectInvoiceType extends AbstractType
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
                'disabled' => true,
            ])
            ->add('object', EntityType::class, [
                'class' => WareObjects::class,
                'choice_label' => 'header',
                'disabled' => true,
            ])
            ->add('reverseVat')
            ->add('note', TextareaType::class, [
                'required' => false,
            ])
            ->add('submit', SubmitType::class)
            ->add('total', HiddenType::class, [
                'data' => 0,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BuhInvoices::class,
        ]);
    }
}
