<?php

namespace App\Form;

use App\Entity\Teacher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

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
                'email',
                EmailType::class,
                [
                    'required' => false,
                    'purify_html' => true,
                ]
            )
            ->add(
                'subject',
                TextType::class,
                [
                    'required' => false,
                    'purify_html' => true,
                    'label' => 'Matière(s)',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Teacher::class,
        ]);
    }
}
