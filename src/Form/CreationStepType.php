<?php

namespace App\Form;

use App\Entity\CreationStep;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints as Assert;

class CreationStepType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de l\'étape',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le titre est requis']),
                    new Assert\Length(['min' => 3, 'max' => 255])
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Description de l\'étape',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La description est requise']),
                    new Assert\Length(['min' => 10])
                ]
            ])
            ->add('images', FileType::class, [
                'label' => 'Images de l\'étape',
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'constraints' => [
                    new Assert\All([
                        new Assert\File([
                            'maxSize' => '5M',
                            'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
                            'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG, WebP, GIF)',
                        ])
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CreationStep::class,
        ]);
    }
}
