<?php

/*
 * (c) 411 Marketing
 */

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AbsoluteTimePickerType.
 */
class AbsoluteTimePickerType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'model_timezone' => 'UTC',
            'view_timezone' => 'UTC',
            'with_seconds' => false,
            'widget' => 'single_text',
            'html5' => false,
            'attr' => [
                'data-provide' => 'timepicker',
                'data-show-seconds' => 'false',
                'data-default-time' => '00:00',
                'data-minute-step' => 5,
                'data-show-meridian' => 'false',
            ],
        ]);
    }

    /**
     * @return null|string
     */
    public function getParent()
    {
        return TimeType::class;
    }
}
