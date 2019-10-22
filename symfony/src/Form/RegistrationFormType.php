<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'placeholder' => 'form.registration.email.placeholder',
                ],
                'constraints' => [
                    new Email([
                        'mode' => Email::VALIDATION_MODE_HTML5,
                    ]),
                ],
                'label' => 'form.registration.email.label',
            ])
            ->add('plainPassword', RepeatedType::class, [
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
                    'label' => 'form.registration.plainPassword.first.label',
                    'attr' => [
                        'placeholder' => 'form.registration.plainPassword.first.placeholder',
                    ],
                ],
                'second_options' => [
                    'label' => 'form.registration.plainPassword.second.label',
                    'attr' => [
                        'placeholder' => 'form.registration.plainPassword.second.placeholder',
                    ],
                ],
                'mapped' => false,
                'type' => PasswordType::class,
            ])
            ->add('firstName', TextType::class, [
                'attr' => [
                    'placeholder' => 'form.registration.firstName.placeholder',
                ],
                'label' => 'form.registration.firstName.label',
            ])
            ->add('lastName', TextType::class, [
                'attr' => [
                    'placeholder' => 'form.registration.lastName.placeholder',
                ],
                'label' => 'form.registration.lastName.label',
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
                'label' => 'form.registration.agreeTerms.label',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
