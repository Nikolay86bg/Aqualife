<?php

/*
 * (c) 411 Marketing
 */

namespace App\Form;

use App\Entity\Facility;
use App\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


/**
 * Class AccountFilterType.
 */
class ScheduleFilterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('from', DatePickerType::class, [
                'label' => 'From:',
                'attr' => [
                    'placeholder' => 'From Date',
                    'class' => 'form-control'
                ],
                'data' => new \DateTime(),
            ])
            ->add('to', DatePickerType::class, [
                'label' => 'Until:',
                'attr' => [
                    'placeholder' => 'To Date',
                    'class' => 'form-control'
                ],
                'data' => new \DateTime(),
            ])
            ->add('facility', EntityType::class, [
                'required' => false,
                'label' => 'Facility:',
                'attr' => [
                    'placeholder' => 'Facility',
                    'class' => 'form-control'
                ],
                'class' => Facility::class
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
