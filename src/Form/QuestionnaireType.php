<?php

namespace App\Form;

use App\Entity\Questionnaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class QuestionnaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'purify_html' => true,
                    'label' => 'Titre',
                ]
            )
            ->add(
                'imageFile',
                VichImageType::class,
                [
                    'label' => 'Choisissez votre image :',
                    'delete_label' => 'Supprimer l\'ancienne image.',
                    'imagine_pattern' => 'thumb',
                    'required' => false,
                ]
            )
            ->add(
                'difficulty',
                ChoiceType::class,
                [
                    'choices' => [
                        'Facile' => 'facile',
                        'Moyen' => 'moyen',
                        'Difficile' => 'difficile',
                    ],
                    'label' => 'Difficulté',
                ]
            )
            ->add(
                'visibility',
                ChoiceType::class,
                [
                    'choices' => [
                        'Oui' => true,
                        'Non' => false,
                    ],
                    'label' => 'Rendre visible',
                ]
            )
            ->add(
                'playable',
                ChoiceType::class,
                [
                    'choices' => [
                        'Oui' => true,
                        'Non' => false,
                    ],
                    'label' => 'Rendre juable',
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Questionnaire::class,
            ]
        );
    }
}
