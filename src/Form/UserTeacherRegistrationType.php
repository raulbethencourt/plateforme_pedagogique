<?php

namespace App\Form;

use App\Entity\Teacher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserTeacherRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class)
            ->add('password', TextType::class)
            ->add('surname', TextType::class)
            ->add('name', TextType::class)
            ->add('email', EmailType::class)
            ->add('photo_name', TextType::class)
            ->add('isVerified', ChoiceType::class, [
                'choices' => [
                    'yes' => true,
                    'no' => false
                ]
            ])
            ->add('entry_date', DateType::class, [

            ])
            ->add('subject')
            ->add('classrooms')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Teacher::class,
        ]);
    }
}
