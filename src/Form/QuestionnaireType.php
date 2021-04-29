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
                        'Compréhension écrite' => 'cEcrite',
                        'Compréhension orale' => 'cOrale',
                        'Expression écrite' => 'eEcrite',
                        'Lexique' => 'lexique',
                        'Phonétique' => 'phonetique',
                        'Grammaire' => 'grammaire',
                    ],
                    'label' => 'Categorie',
                ]
            )
            ->add(
                'level',
                ChoiceType::class,
                [
                    'choices' => [
                        'Alpha 1' => 'alpha1',
                        'Alpha 2' => 'alpha2',
                        'A1.1' => 'a1.1',
                        'A1' => 'a1',
                        'A2' => 'a2',
                        'B1' => 'b1',
                    ],
                    'label' => 'Niveau',
                ]
            )
            ->add(
                'type',
                ChoiceType::class,
                [
                    'choices' => [
                        'QCM ' => 'qcm',
                        'Vrai/faux 2' => 'vf',
                        'Text a trous' => 'tous',
                    ],
                    'label' => 'Tipe de activité',
                ]
            )

            ->add(
                'realisation_time',
                ChoiceType::class,
                [
                    'choices' => [
                        '2 mnts' => 2,
                        '5 mnts' => 5,
                        '10 mnts' => 10,
                        '15 mnts' => 15,
                        '20 mnts' => 20,
                        '30 mnts' => 30,
                    ],
                    'label' => 'Temps de realisation',
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
