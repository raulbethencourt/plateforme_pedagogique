<?php

namespace App\Form;

use App\Entity\Classroom;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ClassroomType
 * Form to creat a Classroom.
 */
class ClassroomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'Nom',
                    'purify_html' => true,
                ]
            )
            ->add(
                'discipline',
                ChoiceType::class,
                [
                    'label' => 'Niveau',
                    'choices' => [
                        'NS 1' => 'NS 1',
                        'NS 2' => 'NS 2',
                        'A1.1' => 'A1.1',
                        'A1' => 'A1',
                        'A2' => 'A2',
                        'B1' => 'B1',
                        'B2' => 'B2',
                        'CléA' => 'CléA',
                        'Voltaire' => 'Voltaire',
                    ],
                ]
            )
            ->add(
                'location',
                ChoiceType::class,
                [
                    'label' => 'Localisation',
                    'choices' => [
                        'Koenigshoffen' => 'Koenigshoffen',
                        'Le Marais' => 'Le marais',
                        'Hautepierre' => 'Hautepierre',
                        'Autre' => 'Autre',
                    ],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Classroom::class,
            ]
        );
    }
}
