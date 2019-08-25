<?php

namespace App\Form\Lobby;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

class NameType extends AbstractType
{
	/**
	 * @inheritdoc
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name', TextType::class, [
				'label' => 'Enter your name',
				'constraints' => [new Length(['min' => 2, 'max' => 30])]
			]);
	}

	/**
	 * @inheritdoc
	 */
	public function getBlockPrefix()
	{
		return 'lobby_name';
	}
}
