<?php

namespace App\Form;

use App\Entity\Questionnaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

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
                'instructions',
                TextareaType::class,
                [
                    'label' => 'Consigne',
                    'required' => false,
                ]
            )
            ->add(
                'imageFile',
                VichImageType::class,
                [
                    'label' => 'Choisissez votre image :',
                    'allow_delete' => false,
                    'imagine_pattern' => 'questionnaire',
                    'required' => false,
                ]
            )
            ->add(
                'link_description',
                TextType::class,
                [
                    'purify_html' => true,
                    'label' => 'Descrition du lien',
                    'required' => false,
                ]
            )
            ->add(
                'link',
                TextType::class,
                [
                    'purify_html' => true,
                    'label' => 'Lien',
                    'constraints' => [
                        new Regex(
                            [
                                'pattern' => '$https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)?$',
                                'message' => 'Votre lien doit commencer avec http:// ou https:// et doit finir avec un pointquelquechose.',
                            ]
                        ),
                    ],
                    'attr' => [
                        'placeholder' => 'ex: https://contact-promotion.org',
                    ],
                    'required' => false,
                ]
            )
            ->add(
                'difficulty',
                ChoiceType::class,
                [
                    'choices' => [
                        'Compréhension écrite' => 'Compréhension écrite',
                        'Compréhension orale' => 'Compréhension orale',
                        'Expression écrite' => 'Expression écrite',
                        'Lexique' => 'Lexique',
                        'Phonétique' => 'Phonetique',
                        'Grammaire' => 'Grammaire',
                    ],
                    'label' => 'Catégorie',
                ]
            )
            ->add(
                'level',
                ChoiceType::class,
                [
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
                    'label' => 'Type d\'activité',
                ]
            )
            ->add(
                'realisation_time',
                ChoiceType::class,
                [
                    'choices' => [
                        '2 min' => 2,
                        '5 min' => 5,
                        '10 min' => 10,
                        '15 min' => 15,
                        '20 min' => 20,
                        '30 min' => 30,
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
