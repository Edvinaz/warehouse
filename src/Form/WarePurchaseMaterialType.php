<?php

namespace App\Form;

use App\Entity\Transport\Transport;
use App\Entity\Purchases\WareInvoices;
use App\Entity\Objects\WareObjects;
use App\Entity\Purchases\WarePurchasedMaterials;
use App\Repository\TransportRepository;
use App\Repository\WareObjectsRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WarePurchaseMaterialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('material', WareMaterialType::class, [
                'disabled' => $options['is_edit'],
            ])
            ->add('quantity', TextType::class, [
                'attr' => [
                    'autocomplete' => 'off'
                ]
            ])
            ->add('price', TextType::class, [
                'attr' => [
                    'autocomplete' => 'off'
                ]
            ])
            ->add('vat', ChoiceType::class, [
                'choices' => [
                    '21 %' => 21,
                    '9 %' => 9,
                    '0 %' => 0,
                ],
            ])
            ->add('balance', HiddenType::class)
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
                $data = $event->getData();
                $form = $event->getForm();
                if (!is_null($data->getMaterial()) && 1 === $data->getMaterial()->getCategoryType()) {
                    $form->add('object', EntityType::class, [
                        'class' => WareObjects::class,
                        'choice_label' => 'header',
                        'query_builder' => function (WareObjectsRepository $repository) {
                            return $repository->createQueryBuilder('wo')
                                ->andWhere('wo.status = :status')
                                ->setParameter('status', 'EXECUTING')
                            ;
                        },
                        'required' => false,
                    ]);
                }
                if (!is_null($data->getMaterial()) && 11 === $data->getMaterial()->getCategoryType()) {
                    $form->add('note', EntityType::class, [
                        'required' => true,
                        'class' => Transport::class,
                        'query_builder' => function (TransportRepository $transportRepository) {
                            return $transportRepository->createQueryBuilder('w');
                        },
                    ]);
                }
                $form->add('submit', SubmitType::class);
            })
            ->add('invoice', EntityType::class, [
                'class' => WareInvoices::class,
                'disabled' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WarePurchasedMaterials::class,
            'is_edit' => true,
        ]);
    }
}
