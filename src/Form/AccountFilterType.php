<?php

/*
 * (c) 411 Marketing
 */

namespace App\Form;

use App\Entity\Department;
use App\Entity\Position;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AccountFilterType.
 */
class AccountFilterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => false,
                'label' => 'filter.name',
                'attr' => [
                    'placeholder' => 'filter.name',
                    'class' => 'form-control'
                ],
            ])
//            ->add('agent', TextType::class, [
//                'required' => false,
//                'label' => 'Agent:',
//                'attr' => [
//                    'placeholder' => 'Agent',
//                ],
//            ])
//            ->add('sport', TextType::class, [
//                'required' => false,
//                'label' => 'Sport:',
//                'attr' => [
//                    'placeholder' => 'Sport',
//                ],
//            ])
//            ->add('country', CountryType::class, [
//                'required' => false,
//                'label' => 'Country:',
//                'attr' => [
//                    'placeholder' => 'Country',
//                ],
//            ])
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
