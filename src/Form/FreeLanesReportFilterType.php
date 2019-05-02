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
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FreeLanesReportFilterType.
 */
class FreeLanesReportFilterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('from', DatePickerType::class, [
                'required' => false,
                'label' => 'filter.from',
                'attr' => [
                    'placeholder' => 'filter.from',
                    'class' => 'form-control datepicker'
                ],
            ])
            ->add('to', DatePickerType::class, [
                'required' => false,
                'label' => 'filter.to',
                'attr' => [
                    'placeholder' => 'filter.to',
                    'class' => 'form-control datepicker'
                ],
            ])
            ->add('lanesNeeded', IntegerType::class, [
                'required' => false,
                'label' => 'label.total_lanes',
                'attr' => [
                    'placeholder' => 'label.total_lanes',
                    'class' => 'form-control'
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
