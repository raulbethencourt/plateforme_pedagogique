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
                        'Alpha 1' => 'alpha1',
                        'Alpha 2' => 'alpha2',
                        'A1.1' => 'a1.1',
                        'A1' => 'a1',
                        'A2' => 'a2',
                        'B1' => 'b1',
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
