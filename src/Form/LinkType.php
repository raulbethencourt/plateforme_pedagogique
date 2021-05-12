<?php

namespace App\Form;

use App\Entity\Link;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class LinkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'Nom du lien',
                    'required' => true,
                ]
            )
            ->add(
                'link',
                TextType::class,
                [
                    'label' => 'Lien',
                    'required' => true,
                    'constraints' => [
                        new Regex(
                            [
                                'pattern' => '$https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)?$',
                                'message' => 'Votre url doit commencer avec http or https et avoir . quelque chose',
                            ]
                        ),
                    ],
                    'attr' => [
                        'placeholder' => 'ex: https://contact-promotion.org',
                    ],
                ]
            )
            ->add(
                'category',
                ChoiceType::class,
                [
                    'label' => 'Categories',
                    'choices' => [
                        'Images' => 'images',
                        'Pédagogie' => 'pedagogie',
                        'Autres' => 'autres',
                    ],
                ]
            )
            ->add(
                'visibility',
                CheckboxType::class,
                [
                    'label' => 'Rendre le lien visible',
                    'required' => false,
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Link::class,
        ]);
    }
}
