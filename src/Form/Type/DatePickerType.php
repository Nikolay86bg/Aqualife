<?php

/*
 * (c) 411 Marketing
 */

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DatePickerType.
 */
class DatePickerType extends AbstractType
{
    const DATE_FORMAT = 'dd-MM-yyyy';

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'required' => false,
            'widget' => 'single_text',
            'attr' => [
                'class' => 'datepicker',
            ],
            'format' => static::DATE_FORMAT,
            'html5' => false,
        ]);
    }

    /**
     * @return null|string
     */
    public function getParent()
    {
        return DateType::class;
    }
}
