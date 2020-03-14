<?php

declare(strict_types=1);

namespace App\Form\Authentication;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

class UserLoginType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('_username', TextType::class, [
				'label' => 'Username',
				'constraints' => [new Length(['min' => 2, 'max' => 30])]
			])
			->add('_password', PasswordType::class, [
				'label' => 'Password'
			]);
	}

	public function getBlockPrefix()
	{
		return 'authenticate_user';
	}
}
