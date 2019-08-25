<?php

namespace App\Form\Lobby;

use App\Entity\Player;
use App\Form\PlayerFieldTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvitePlayersType extends AbstractType
{
	use PlayerFieldTrait;

	/**
	 * @inheritdoc
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('players', ChoiceType::class, [
				'choices' => $options['players'],
				'choice_label' => $this->getPlayerLabelCallback(),
				'choice_value' => $this->getPlayerValueCallback(),
				'multiple' => true,
				'expanded' => true,
				'required' => false
			]);
	}

	/**
	 * @inheritdoc
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		parent::configureOptions($resolver);

		$resolver->setDefaults([
			'players' => [],
		]);
	}

	/**
	 * @inheritdoc
	 */
	public function getBlockPrefix()
	{
		return 'lobby_select_players';
	}
}