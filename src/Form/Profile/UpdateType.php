<?php

declare(strict_types=1);

namespace App\Form\Profile;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class UpdateType extends AbstractType
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'playername' => null,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'profile_update';
    }
}
