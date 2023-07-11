<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'username',
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank(
                            [
                                'message' => 'Vous devez ajouter un nom d\'utilisateur·rice.',
                            ]
                        ),
                        new Length(
                            [
                                'min' => 3,
                                'minMessage' => 'Votre nom d\'utilisateur·rice doit contenir au minimum {{ limit }} caractères.',
                                'max' => 15,
                                'maxMessage' => 'Votre nom d\'utilisateur·rice doit contenir au maximum {{ limit }} caractères.',
                            ]
                        ),
                    ],
                    'purify_html' => true,
                    'label' => 'Nom d\'utilisateur',
                ]
            )
            ->add(
                'surname',
                TextType::class,
                [
                    'required' => false,
                    'purify_html' => true,
                    'label' => 'Nom',
                ]
            )
            ->add(
                'name',
                TextType::class,
                [
                    'required' => false,
                    'purify_html' => true,
                    'label' => 'Prénom',
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'constraints' => [
                        new NotBlank(
                            [
                                'message' => 'Vous devez ajouter une adresse email.',
                            ]
                        ),
                    ],
                    'purify_html' => true,
                ]
            )
            ->add(
                'agreeTerms',
                CheckboxType::class,
                [
                    'label' => 'Acceptez les conditions d\'utilisation',
                    'mapped' => false,
                    'constraints' => [
                        new IsTrue(
                            [
                                'message' => 'Vous devez accepter les conditions d\'utilisation.',
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'plainPassword',
                PasswordType::class,
                [
                    'label' => 'Mot de passe',
                    'mapped' => false,
                    'constraints' => [
                        new NotBlank(
                            [
                                'message' => 'Vous devez ajouter un mot de passe.',
                            ]
                        ),
                        new Regex(
                            [
                                'pattern' => '/^(?=.*\\d)(?=.*[a-z])(?=.*[A-Z]).{4,10}$/i',
                            ]
                        ),
                    ],
                    'purify_html' => true,
                ]
            )
            ->add(
                'telephone',
                TelType::class,
                [
                    'label' => 'Téléphone',
                    'purify_html' => true,
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'ex: 0768513172',
                    ],
                    'constraints' => [
                        new Regex(
                            [
                                'pattern' => '/^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/',
                                'message' => 'Votre numéro doit contenir 10 chiffres et commencer par 0.',
                            ]
                        ),
                    ],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
            ]
        );
    }
}
