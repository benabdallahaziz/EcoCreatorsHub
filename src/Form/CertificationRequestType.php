<?php

namespace App\Form;

use App\Entity\CertificationRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CertificationRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('motivation', TextareaType::class, [
                'label' => 'Votre motivation',
                'required' => false,
            ])
            ->add('portfolio', TextareaType::class, [
                'label' => 'Portfolio (liens séparés par des virgules)',
                'required' => false,
            ])
            ->add('documents', FileType::class, [
                'label' => 'Documents (fichiers multipliés)',
                'multiple' => true,
                'mapped' => false, // gestion dans le controller
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CertificationRequest::class,
        ]);
    }
}
