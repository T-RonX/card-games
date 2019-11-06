<?php

namespace App\Form\Duizenden;

use App\Entity\Player;
use App\Form\PlayerFieldTrait;
use App\Repository\PlayerRepository;
use App\Shufflers\ShufflerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateGameType extends AbstractType
{
	use PlayerFieldTrait;

	/**
	 * @inheritdoc
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		if (null == $options['by_invitation'])
		{
			$builder->add('players', ChoiceType::class, [
				'choices' => $options['available_players'],
				'data' => $options['available_players'],
				'choice_label' => $this->getPlayerLabelCallback(),
				'choice_value' => $this->getPlayerValueCallback(),
				'multiple' => true,
			]);
		}

		$builder
			->add('initial_shuffle', CheckboxType::class, [
				'required' => false,
				'label' => 'Shuffle deck before start',
				'data' => true,
			])
			->add('initial_shuffle_algorithm', ChoiceType::class, [
				'choices' => [
					'Overhand' => ShufflerType::OVERHAND(),
					'Random' => ShufflerType::RANDOM()
				],
				'label' => 'Shuffle algorithm',
			])
			->add('is_dealer_random', CheckboxType::class, [
				'required' => false,
				'label' => 'Random dealing player',
				'data' => true,
			])
			->add('first_dealer', ChoiceType::class, [
				'choices' => $options['available_players'],
				'choice_label' => $this->getPlayerLabelCallback(),
				'choice_value' => $this->getPlayerValueCallback(),
			])
			->add('target_score', IntegerType::class, [
				'label' => 'Target score',
				'data' => 1000
			])
			->add('first_meld_minimum_points', IntegerType::class, [
				'label' => 'Minimum points of first meld',
				'data' => 30
			])
			->add('round_finish_extra_points', IntegerType::class, [
				'label' => 'Extra points for finishing a round',
				'data' => 0
			]);
	}

	/**
	 * @inheritdoc
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		parent::configureOptions($resolver);

		$resolver->setDefaults([
			'available_players' => [],
			'by_invitation' => false
		]);
	}

	/**
	 * @inheritdoc
	 */
	public function getBlockPrefix()
	{
		return 'duizenden_create-game';
	}
}