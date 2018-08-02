<?php

/*
 * (c) 411 Marketing
 */

namespace App\Form;

use App\Entity\Query;
use App\Form\Type\DatePickerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

/**
 * Class QueryType.
 */
class QueryType extends AbstractType
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
            ->add('manager',null, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('sport',null, [
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            ->add('country', CountryType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
             ])
            ->add('hotel', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'choices' => [
                    Query::HOTELS[Query::HOTEL1] => Query::HOTEL1 ,
                    Query::HOTELS[Query::HOTEL2] => Query::HOTEL2 ,
                    Query::HOTELS[Query::HOTEL3] => Query::HOTEL3 ,
                ]
            ])

//            ->add('dateOfArrival', DatePickerType::class, [
//                'label' => 'Arrive:',
//                'attr' => [
//                    'placeholder' => 'Arrive',
//                ],
//                'data' => new \DateTime(),
//            ])
//            ->add('dateOfDeparture', DatePickerType::class, [
//                'label' => 'Departure:',
//                'attr' => [
//                    'placeholder' => 'Departure',
//                ],
//                'data' => new \DateTime(),
//            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {

        $resolver->setDefaults([
            'data_class' => 'App\Entity\Query',
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'appbundle_query';
    }
}
