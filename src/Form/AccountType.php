<?php

/*
 * (c) 411 Marketing
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

/**
 * Class UserType.
 */
class AccountType extends AbstractType
{
    protected $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('agent',null, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('sport',null, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('country', CountryType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
             ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {

        $resolver->setDefaults([
            'data_class' => 'App\Entity\Account',
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'appbundle_account';
    }
}
