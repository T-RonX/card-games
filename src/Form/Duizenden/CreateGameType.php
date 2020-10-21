<?php

declare(strict_types=1);

namespace App\Form\Duizenden;

use App\Form\PlayerFieldTrait;
use App\Shufflers\ShufflerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            ->add('ai_players', IntegerType::class, [
                'label' => 'AI Players',
                'data' => 0,
            ])
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
            ])
            ->add('allow_first_turn_round_end', CheckboxType::class, [
                'required' => false,
                'label' => 'Allow round finish in first turn',
                'data' => false
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