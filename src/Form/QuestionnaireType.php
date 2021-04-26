<?php

namespace App\Form;

use App\Entity\Questionnaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
                        'Grammaire' => 'grammaire',
                        'Sintaxe' => 'sintaxe',
                        'Comprension' => 'comprension',
                        'Expression' => 'expression', 
                    ],
                    'label' => 'Categorie',
                ]
            )
            ->add(
                'visibility',
                CheckboxType::class,
                [
                    'label' => 'Rendre visible',
                    'required' => false,
                ]
            )
            ->add(
                'playable',
                CheckboxType::class,
                [
                    'label' => 'Rendre juable',
                    'required' => false,
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
