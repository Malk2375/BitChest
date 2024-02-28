<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;


class UserPasswordType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('plainPassword', RepeatedType::class, [
        'type' => PasswordType::class,
        'first_options' => [
          'label' => 'Password',
          'label_attr' => [
            'class' => 'form-label mt-4',
          ],
          'attr' => [
            'class' => 'form-control',
          ],
        ],
        'second_options' => [
          'label' => 'Password Confirmation',
          'label_attr' => [
            'class' => 'form-label mt-4',
          ],
          'attr' => [
            'class' => 'form-control',
          ],
        ],
        'invalid_message' => "Passwords don't match",
      ])
      ->add('newPassword', PasswordType::class, [
        'label' => 'New Password',
        'label_attr' => [
          'class' => 'form-label mt-4',
        ],
        'attr' => [
          'class' => 'form-control',
        ],
      ])
      ->add('submit', SubmitType::class, [
        'attr' => [
          'class' => 'btn btn-success mt-4',
        ]
      ]);
  }
}
