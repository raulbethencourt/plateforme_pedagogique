<?php

namespace App\Form;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'Titre',
                    'attr' => [
                        'class' => 'titleQuestionnaire',
                        'placeholder' => "Entrée l'intitulé de la question",
                    ],
                    'purify_html' => true,
                ]
            )
            ->add(
                'propositions',
                CollectionType::class,
                [
                    'entry_type' => PropositionType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'label' => false,
                    'by_reference' => false,
                ]
            )
            ->add(
                'score',
                NumberType::class,
                [
                    'attr' => [
                        'class' => 'score',
                    ],
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Ajouter cette question',
                    'attr' => ['class' => 'btn-secondary'],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Question::class,
            ]
        );
    }
}
