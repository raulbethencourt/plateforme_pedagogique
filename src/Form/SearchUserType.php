<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'surname',
                TextType::class,
                [
                    'label' => false,
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Nom...',
                    ],
                ]
            )
            ->add(
                'name',
                TextType::class,
                [
                    'label' => false,
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Prenom...',
                    ],
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => false,
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Email...',
                    ],
                ]
            )
            ->add(
                'telephone',
                TelType::class,
                [
                    'label' => false,
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Téléphone...',
                        'class' => 'mr-3',
                    ],
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Chercher',
                    'attr' => [
                        'class' => 'btn btn-primary',
                    ],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'method' => 'get',
            'csrf_protection' => false,
        ]);
    }
}
