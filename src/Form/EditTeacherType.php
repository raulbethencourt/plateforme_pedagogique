<?php

namespace App\Form;

use App\Entity\Teacher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class EditTeacherType extends AbstractType
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
                                'message' => 'Please enter a username',
                            ]
                        ),
                        new Length(
                            [
                                'min' => 3,
                                'minMessage' => 'Your username should be at least {{ limit }} characters',
                                'max' => 15,
                                'maxMessage' => 'This username should be less than {{ limit }} characters',
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
                                'message' => 'Votre numero doit faire 10 digits',
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'required' => false,
                    'purify_html' => true,
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Teacher::class,
        ]);
    }
}
