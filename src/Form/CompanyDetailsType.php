<?php

namespace App\Form;

use App\Models\Company;
use App\Models\CompanyUpdate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyDetailsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('code')
            ->add('vat')
            ->add('phone')
            ->add('mobile')
            ->add('email')
            ->add('street')
            ->add('city')
            ->add('postalCode')
            ->add('position')
            ->add('boss')
            ->add('bank')
            ->add('bankCode')
            ->add('account')
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CompanyUpdate::class,
        ]);
    }
}
