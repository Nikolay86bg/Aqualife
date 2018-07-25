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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AccountEventFilterType.
 */
class UserFilterType extends AbstractType
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
                'label' => 'Name:',
                'attr' => [
                    'placeholder' => 'Name',
                ],
            ])
//            ->add('department', EntityType::class, [
//                'required' => false,
//                'placeholder' => '- Select Department -',
//                'label' => 'Department:',
//                'class' => Department::class,
//            ])
            ->add('status', ChoiceType::class, [
                'required' => false,
                'placeholder' => '- Select Status -',
                'label' => 'Status:',
                'choices' => [
                    'Is Active' => 1,
                    'Not Active' => 0,
                ],
            ])
            ->add('report_to', EntityType::class, [
                'required' => false,
                'class' => User::class,
                'placeholder' => '- Select Supervisor -',
                'label' => 'Report to:',
            ])
//            ->add('position', EntityType::class, [
//                'required' => false,
//                'placeholder' => '- Select Position -',
//                'label' => 'Position:',
//                'class' => Position::class,
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
