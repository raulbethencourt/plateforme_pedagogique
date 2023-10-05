<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchQuestionnaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'label' => false,
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Titre...',
                    ],
                ]
            )
            ->add(
                'level',
                ChoiceType::class,
                [
                    'label' => false,
                    'placeholder' => 'Niveau...',
                    'choices' => [
                        'Alpha 1' => 'Alpha1',
                        'Alpha 2' => 'Alpha2',
                        'A1.1' => 'A1.1',
                        'A1' => 'A1',
                        'A2' => 'A2',
                        'B1' => 'B1',
                        'B2' => 'B2',
                        'CléA' => 'CléA',
                        'Voltaire' => 'Voltaire',
                    ],
                    'required' => false,
                ]
            )
            ->add(
                'category',
                ChoiceType::class,
                [
                    'label' => false,
                    'placeholder' => 'Type...',
                    'choices' => [
                        'Compréhension écrite' => 'Compréhension écrite',
                        'Compréhension orale' => 'Compréhension orale',
                        'Expression écrite' => 'Expression écrite',
                        'Lexique' => 'Lexique',
                        'Phonétique' => 'Phonetique',
                        'Grammaire' => 'Grammaire',
                    ],
                    'required' => false,
                ]
            )
            ->add(
                'author',
                TextType::class,
                [
                    'label' => false,
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Auteur·rice...',
                    ],
                ]
            )
            ->add(
                'date',
                DateType::class,
                [
                    'label' => false,
                    'required' => false,
                    'widget' => 'choice',
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'get',
            'csrf_protection' => false,
        ]);
    }
}
