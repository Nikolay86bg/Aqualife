<?php

/*
 * (c) 411 Marketing
 */

namespace App\Form;

use App\Entity\Department;
use App\Entity\Position;
use App\Entity\Query;
use App\Entity\User;
use App\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class QueryFilterType.
 */
class QueryFilterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', ChoiceType::class, [
                'required' => false,
                'label' => 'filter.status',
                'choices' => array_flip(Query::STATUSES),
                'attr' => [
                    'placeholder' => 'filter.status',
                    'class' => 'form-control'
                ],
            ])
            ->add('sport', TextType::class, [
                'required' => false,
                'label' => 'filter.sport',
                'attr' => [
                    'placeholder' => 'filter.sport',
                    'class' => 'form-control'
                ],
            ])
            ->add('name', TextType::class, [
                'required' => false,
                'label' => 'filter.name',
                'attr' => [
                    'placeholder' => 'filter.name',
                    'class' => 'form-control'
                ],
            ])
            ->add('country', CountryType::class, [
                'required' => false,
                'label' => 'filter.country',
                'attr' => [
                    'placeholder' => 'filter.country',
                    'class' => 'form-control'
                ],
            ])
            ->add('from', DatePickerType::class, [
                'required' => false,
                'label' => 'filter.arrival',
                'attr' => [
                    'placeholder' => 'filter.from',
                    'class' => 'form-control datepicker'
                ],
            ])
            ->add('to', DatePickerType::class, [
                'required' => false,
                'label' => 'filter.departure',
                'attr' => [
                    'placeholder' => 'filter.to',
                    'class' => 'form-control datepicker'
                ],
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'allow_extra_fields' => true,
            'csrf_protection' => false,
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return null;
    }
}
