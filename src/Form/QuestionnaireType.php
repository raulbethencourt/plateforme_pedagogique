<?php

namespace App\Form;

use App\Entity\Questionnaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

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
                    'label' => 'Difficulté'
                ]
            );
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
