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
 * Form to creat a Classroom
 * @package App\Form
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
                    'choices' => [
                        'Arts' => 'arts',
                        'Français' => 'français',
                        'Histoire' => 'histoire',
                        'Musique' => 'musique',
                        'Mathematiques' => 'mathematiques',
                        'Informatique' => 'informatique',
                        'Philosophie' => 'philosophie',
                        'Chimie' => 'chimie',
                        'Physique' => 'physique',
                    ],
                ]
            );
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
