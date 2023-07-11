<?php

namespace App\Form;

use App\Entity\Avatar;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class AvatarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'imageFile',
                VichImageType::class,
                [
                    'label' => 'Choisissez votre image :',
                    'allow_delete' => false,
                    'imagine_pattern' => 'profile'
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Ajouter',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Avatar::class,
            ]
        );
    }
}
