<?php

namespace App\Form;

use App\Models\Transport\TransportDetails;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransportDetailsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('main_fuel', ChoiceType::class, [
                'choices' => [
                    'Benzinas' => 21,
                    'Dyzelinas' => 22,
                    'Dujos' => 23,
                ],
            ])
            ->add('secondary_fuel', ChoiceType::class, [
                'choices' => [
                    '' => 0,
                    'Benzinas' => 21,
                    'Dyzelinas' => 22,
                    'Dujos' => 23,
                ],
                'required' => false,
            ])
            ->add('purchased', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('sold', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('insurance', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('roadTax', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('defaultFuelNorm')
            ->add('secondaryFuelIndex')
            ->add('tachometerPurchased')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TransportDetails::class,
        ]);
    }
}
