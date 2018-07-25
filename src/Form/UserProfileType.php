<?php

/*
 * (c) 411 Marketing
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserType.
 */
class UserProfileType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('plainPassword', PasswordType::class, [
                'required' => false,
                'label' => 'Password',
            ])
            ->add('timezone', ChoiceType::class, [
                'choices' => [
                    'Coordinated Universal Time' => 'UTC',
                    'Bulgaria' => 'Europe/Sofia',
                    'New York' => 'America/New_York',
                    'PHL' => 'Asia/Manila',
                ],
            ])
            ->add('email')
            ->add('firstName')
            ->add('lastName');
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\User',
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'appbundle_user';
    }
}
