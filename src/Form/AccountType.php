<?php

/*
 * (c) 411 Marketing
 */

namespace App\Form;

use App\Entity\Account;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
                'label' => "filter.name",
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "filter.name",
                ]
            ])
            ->add('manager',null, [
                'label' => "filter.manager",
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "filter.manager",
                ]
            ])
            ->add('sport', ChoiceType::class, [
                'label' => "filter.sport",
                'choices' => array_flip(Account::SPORTS),
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "filter.sport",
                ]
            ])
            ->add('country', CountryType::class, [
                'label' => "filter.country",
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "filter.sport",
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
