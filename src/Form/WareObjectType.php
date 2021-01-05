<?php

namespace App\Form;

use App\Entity\Contrahents;
use App\Entity\Objects\WareObjects;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WareObjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('number', IntegerType::class, [
                'required' => true,
                'label' => 'Number',
            ])
            ->add('contrahent', EntityType::class, [
                'class' => Contrahents::class,
                'required' => true,
                'label' => 'Contrahent',
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'Name',
            ])
            ->add('adress', TextType::class, [
                'required' => true,
                'label' => 'Address',
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Elektros montavimo darbai' => 'ELECTRICAL_INSTALLATION_WORK',
                    'Elektriniai matavimai' => 'ELECTRICAL_MEASUREMENTS',
                    'Eksploatavimo paslaugos' => 'OPERATING_SERVICES',
                    'Projektavimo darbai' => 'DESIGN_WORK',
                ],
                'required' => true,
                'label' => 'Type',
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Pasiūlymas' => 'OFFER',
                    'Vykdomas' => 'EXECUTING',
                    'Darbai baigti' => 'DONE',
                    'Uždarytas' => 'CLOSED',
                ],
                'required' => true,
                'label' => 'Status',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Submit',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WareObjects::class,
        ]);
    }
}
