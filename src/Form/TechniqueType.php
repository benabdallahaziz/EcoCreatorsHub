<?php

namespace App\Form;

use App\Entity\Technique;
use App\Entity\EcoTip;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class TechniqueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la technique',
                'attr' => ['class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control', 'rows' => 4]
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'Catégorie',
                'choices' => [
                    'Recyclage' => 'Recyclage',
                    'Upcycling' => 'Upcycling',
                    'Éco-Design' => 'Éco-Design',
                    'Art Naturel' => 'Art Naturel',
                    'Zéro Déchet' => 'Zéro Déchet'
                ],
                'attr' => ['class' => 'form-select']
            ])
            ->add('difficulty', ChoiceType::class, [
                'label' => 'Difficulté',
                'choices' => [
                    'Débutant' => 'Débutant',
                    'Intermédiaire' => 'Intermédiaire',
                    'Avancé' => 'Avancé'
                ],
                'attr' => ['class' => 'form-select']
            ])
            ->add('materials', TextareaType::class, [
                'label' => 'Matériaux nécessaires',
                'required' => false,
                'empty_data' => '',
                'attr' => ['class' => 'form-control', 'rows' => 3]
            ])
            ->add('steps', TextareaType::class, [
                'label' => 'Étapes détaillées',
                'required' => false,
                'empty_data' => '',
                'attr' => ['class' => 'form-control', 'rows' => 6]
            ])
            ->add('images', FileType::class, [
                'label' => 'Images',
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'attr' => ['class' => 'form-control']
            ])
            ->add('relatedEcoTips', EntityType::class, [
                'class' => EcoTip::class,
                'choice_label' => 'title',
                'multiple' => true,
                'required' => false,
                'label' => 'Astuces écologiques liées',
                'attr' => ['class' => 'form-select', 'size' => 5]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Technique::class,
        ]);
    }
}