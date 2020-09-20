<?php

namespace App\Form;

use App\Entity\Invite;
use App\Entity\Student;
use App\Entity\Teacher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InviteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => false,
                    'attr' => [
                        'class' => 'm-1',
                        'placeholder' => 'email...',
                    ],
                ]
            )
            ->add(
                'type',
                ChoiceType::class,
                [
                    'label' => false,
                    'choices' => [
                        'teacher' => 'teacher',
                        'student' => 'student',
                    ],
                    'attr' => [
                        'class' => 'm-1 w-100',
                    ],
                ]
            )
            ->add('name', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'name...'
                ]
            ])
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Envoyer',
                    'attr' => [
                        'class' => 'btn btn-secondary',
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Invite::class,
            ]
        );
    }
}
