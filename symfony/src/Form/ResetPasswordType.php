<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['verify_old_password']) {
            $builder->add('old', PasswordType::class, [
                'label' => 'form.reset_password.old.label',
                'attr' => [
                    'placeholder' => 'form.reset_password.old.placeholder',
                ],
            ]);
        }

        $builder
            ->add('new', RepeatedType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        'max' => 4096,
                    ]),
                    new NotCompromisedPassword([
                        'skipOnError' => true,
                    ])
                ],
                'first_options' => [
                    'label' => 'form.reset_password.new.first.label',
                    'attr' => [
                        'placeholder' => 'form.reset_password.new.first.placeholder',
                    ],
                ],
                'second_options' => [
                    'label' => 'form.reset_password.new.second.label',
                    'attr' => [
                        'placeholder' => 'form.reset_password.new.second.placeholder',
                    ],
                ],
                'mapped' => false,
                'type' => PasswordType::class,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'verify_old_password' => true,
        ]);
    }
}