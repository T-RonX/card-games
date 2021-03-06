<?php

declare(strict_types=1);

namespace App\Form\Authentication;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

class UserLoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Username',
                'constraints' => [new Length(['min' => 2, 'max' => 30])]
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password'
            ])
            ->add('remember_me', CheckboxType::class, [
                'required' => false
            ]);
    }

    public function getBlockPrefix()
    {
        return 'authenticate_user';
    }
}
