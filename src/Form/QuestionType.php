<?php

namespace App\Form;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'Titre',
                    'attr' => [
                        'class' => 'titleQuestionnaire',
                        'placeholder' => "Entrer l'intitulé de la question",
                    ],
                    'purify_html' => true,
                ]
            )
            ->add(
                'imageFile',
                VichImageType::class,
                [
                    'label' => 'Choisissez votre image :',
                    'delete_label' => 'Supprimer l\'ancienne image.',
                    'imagine_pattern' => 'question',
                    'required' => false,
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
                ChoiceType::class,
                [
                    'choices' => [
                        '1' => 1,
                        '2' => 2,
                        '3' => 3,
                        '4' => 4,
                        '5' => 5,
                        '6' => 6,
                        '7' => 7,
                        '8' => 8,
                        '9' => 9,
                        '10' => 10,
                    ],
                    'attr' => [
                        'class' => 'score',
                    ],
                    'label' => 'Point(s)',
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Ajouter cette question',
                    'attr' => ['class' => 'btn-primary'],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Question::class,
            ]
        );
    }
}
