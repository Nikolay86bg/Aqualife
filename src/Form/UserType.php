<?php

/*
 * (c) 411 Marketing
 */

namespace App\Form;

use App\Entity\Department;
use App\Entity\Position;
use App\Entity\User;
use App\Form\Type\DatePickerType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

/**
 * Class UserType.
 */
class UserType extends AbstractType
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
            ->add('username', null, [
                'label' => 'label.username',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('plainPassword', PasswordType::class, [
                'required' => false,
                'label' => 'label.password',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('email',null, [
                'label' => 'label.email',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('firstName',null, [
                'label' => 'label.first_name',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('lastName',null, [
                'label' => 'label.last_name',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
//            ->add('parent', EntityType::class, [
//                'label' => 'Parent:',
//                'required' => false,
//                'placeholder' => '- NOBODY -',
//                'class' => User::class,
//                'query_builder' => function (EntityRepository $er) {
//                    return $er->createQueryBuilder('u')
//                        ->orderBy('u.username', 'ASC');
//                },
//                'attr' => [
//                    'class' => 'form-control'
//                ]
//            ])
            ->add('position', ChoiceType::class, [
                'label' => 'label.position',
                'choices' => [
                    'User' => User::ROLE_USER,
                    'Reception' => User::ROLE_RECEPTION,
                    'Manager' => User::ROLE_MANAGER,
                    'Admin' => User::ROLE_ADMIN,
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
//            ->add('department', EntityType::class, [
//                'label' => 'Department:',
//                'required' => true,
//                'class' => Department::class,
//                'query_builder' => function (EntityRepository $er) {
//                    /** @var User $user */
//                    $user = $this->tokenStorage->getToken()->getUser();
//
//                    if (in_array(User::ROLE_ADMIN, $user->getRoles(), true) ||
//                        in_array(User::ROLE_DIRECTOR, $user->getRoles(), true)
//                    ) {
//                        return $er->createQueryBuilder('d')
//                            ->orderBy('d.name', 'ASC');
//                    }
//
//                    /** @var Department $department */
//                    $department = $user->getDepartment();
//
//                    $department_names = USER::MANAGERS_DEPARTMENT_PERMISSIONS[$department->getName()];
//
//                    return $er->createQueryBuilder('d')
//                        ->where('d.name IN(:departments)')
//                        ->setParameter('departments', $department_names)
//                        ->orderBy('d.name', 'ASC');
//                },
//            ])
//            ->add('position', EntityType::class, [
//                'label' => 'Position:',
//                'required' => true,
//                'class' => Position::class,
//                'query_builder' => function (EntityRepository $er) {
//                    /** @var User $user */
//                    $user = $this->tokenStorage->getToken()->getUser();
//
//                    if (in_array(User::ROLE_ADMIN, $user->getRoles(), true) ||
//                        in_array(User::ROLE_DIRECTOR, $user->getRoles(), true)
//                    ) {
//                        return $er->createQueryBuilder('p')
//                            ->orderBy('p.name', 'ASC');
//                    }
//
//                    $positions = [
//                        'Administrator',
//                        'Director',
//                        'Manager',
//                    ];
//
//                    return $er->createQueryBuilder('p')
//                        ->where('p.name NOT IN(:positions)')
//                        ->setParameter('positions', $positions)
//                        ->orderBy('p.name', 'ASC');
//                },
//            ])
            ->add('isActive',null, [
                'label' => 'label.is_active',
                'attr' => [
                    'class' => 'checkbox'
                ],
                'data' => true,
            ])
        ;
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
