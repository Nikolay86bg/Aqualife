<?php

/*
 * (c) 411 Marketing
 */

namespace App\Form\Type;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AccountEventStatusType.
 */
class AccountEventStatusType extends AbstractType
{
    /** @var array */
    private $config;

    /**
     * AccountEventStatusType constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->config = $container->getParameter('account_event');
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => array_keys($this->config),
            'choice_attr' => function ($value) {
                return ['class' => $this->config[$value]['class']];
            },
            'choice_label' => function ($value) {
                return $this->config[$value]['title'];
            },
        ]);
    }

    /**
     * @return null|string
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
