<?php

namespace App\Form;

use App\Entity\Sales\BuhContracts;
use App\Entity\Contrahents;
use App\Entity\Objects\WareObjects;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObjectContractType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'empty_data' => 'statybos rangos',
            ])
            ->add('number', TextType::class, [
                'required' => true,
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('contrahent', EntityType::class, [
                'class' => Contrahents::class,
                'disabled' => true,
                'required' => true,
            ])
            ->add('object', EntityType::class, [
                'class' => WareObjects::class,
                'disabled' => true,
                'choice_label' => 'header',
                'required' => true,
            ])
            ->add('total', TextType::class, [
                'required' => true,
            ])
            ->add('reverseVAT')
            ->add('advance', TextType::class, [
                'data' => '-',
                'required' => true,
            ])
            ->add('begin', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('end', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('billingCondition', TextType::class, [
                'data' => 'visiškai jį užbaigus',
                'required' => true,
            ])
            ->add('billing', TextType::class, [
                'data' => 'atsiskaityti per 10 dienų nuo PVM sąskaitos faktūros pateikimo.',
                'required' => true,
            ])
            ->add('works', TextType::class, [
                'data' => 'sąmatoje numatytus elektros montavimo darbus laikantis esamų normų ir taisyklių.',
                'required' => true,
            ])
            ->add('warranty', TextType::class, [
                'data' => 'penki metai',
                'required' => true,
            ])
            ->add('defaultInterest', TextType::class, [
                'data' => '-',
                'required' => true,
            ])
            ->add('fine', TextType::class, [
                'data' => '-',
                'required' => true,
            ])
            ->add('notes', TextareaType::class, [
                'required' => false,
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BuhContracts::class,
        ]);
    }
}
