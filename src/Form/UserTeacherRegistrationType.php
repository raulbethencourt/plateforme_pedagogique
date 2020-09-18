<?php

namespace App\Form;

use App\Entity\Teacher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserTeacherRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class)
            ->add('surname', TextType::class)
            ->add('name', TextType::class)
            ->add('email', EmailType::class)
            ->add('photo_name', TextType::class)
            ->add(
                'isVerified',
                ChoiceType::class,
                [
                    'choices' => [
                        'yes' => true,
                        'no' => false,
                    ],
                ]
            )
            ->add('subject', TextType::class)
            ->add(
                'agreeTerms',
                CheckboxType::class,
                [
                    'mapped' => false,
                    'constraints' => [
                        new IsTrue(
                            [
                                'message' => 'You should agree to our terms.',
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'plainPassword',
                PasswordType::class,
                [
                    'mapped' => false,
                    'constraints' => [
                        new NotBlank(
                            [
                                'message' => 'Please enter a password',
                            ]
                        ),
                        new Length(
                            [
                                'min' => 6,
                                'minMessage' => 'Your password should be at least {{ limit }} characters',
                                'max' => 4096,
                            ]
                        ),
                        new Regex(
                            [
                                'pattern' => "/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{4,10}$/i",
                            ]
                        ),
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Teacher::class,
            ]
        );
    }
}
