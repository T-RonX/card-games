<?php

declare(strict_types=1);

namespace App\Form\User;

use App\User\Validator\UsernameAvailableConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class CreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('playername', TextType::class, [
                'label' => 'Playername',
                'data' => $options['playername'],
                'constraints' => [
                    new Length(['min' => 2, 'max' => 30]),
                ]
            ])
            ->add('username', TextType::class, [
                'label' => 'Username',
                'data' => $options['username'],
                'constraints' => [
                    new Length(['min' => 2, 'max' => 30]),
                    new UsernameAvailableConstraint()
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'playername' => null,
            'username' => null
        ]);
    }

    public function getBlockPrefix()
    {
        return 'user_create';
    }
}
