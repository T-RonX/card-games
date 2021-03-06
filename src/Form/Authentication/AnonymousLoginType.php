<?php

declare(strict_types=1);

namespace App\Form\Authentication;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

class AnonymousLoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Player name',
                'constraints' => [new Length(['min' => 2, 'max' => 30])]
            ]);
    }

    public function getBlockPrefix()
    {
        return 'authenticate_anonymous';
    }
}
