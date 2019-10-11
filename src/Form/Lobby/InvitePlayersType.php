<?php

namespace App\Form\Lobby;

use App\Form\PlayerFieldTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvitePlayersType extends AbstractType
{
	use PlayerFieldTrait;

	/**
	 * @var string
	 */
	private $current_player_id;

	/**
	 * @inheritdoc
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$this->current_player_id = $options['current_player_id'];

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
			'current_player_id' => null
		]);
	}

	public function finishView(FormView $view, FormInterface $form, array $options)
	{
		parent::finishView($view, $form, $options);

		foreach ($view['players']->children as $child)
		{
			$child->vars['is_current_player'] = $child->vars['value'] === $this->current_player_id;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function getBlockPrefix()
	{
		return 'lobby_select_players';
	}
}