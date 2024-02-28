<?php

namespace App\Form;

use App\Entity\CryptoCurrency;
use App\Entity\Transaction;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class BuyCryptoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('crypto', EntityType::class, [
                'class' => CryptoCurrency::class,
                'choice_label' => 'name',
                'multiple' => false,
                'attr' => ['class' => 'form-select'],
            ])
            ->add('amount', ChoiceType::class, [
                'choices' => [
                    '0.2' => 0.2,
                    '0.4' => 0.4,
                    '0.6' => 0.6,
                    '0.8' => 0.8,
                    '1' => 1,
                ],
                'multiple' => false,
                'attr' => ['class' => 'form-select'],
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary mt-4',
                    'label' => 'Buy',
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
        ]);
    }
}
