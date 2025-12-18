<?php
// src/Form/AdminArtistType.php
namespace App\Form;

use App\Entity\Artist;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminArtistType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Récupération des utilisateurs disponibles passés depuis le controller
        $availableUsers = $options['available_users'] ?? [];

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'artiste',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Nom complet de l\'artiste',
                ],
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choices' => $availableUsers,
                'choice_label' => 'email',
                'placeholder' => 'Sélectionner un utilisateur',
                'required' => true,
                'label' => 'Utilisateur associé',
            ])
            ->add('ecoTechnique', TextType::class, [
                'label' => 'Technique écologique',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex: Recyclage, Upcycling...',
                ],
            ])
            ->add('bio', TextareaType::class, [
                'label' => 'Biographie',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Décrivez le parcours, les techniques et l\'approche écologique de l\'artiste...',
                    'rows' => 6,
                ],
            ])
            ->add('profilePicture', TextType::class, [
                'label' => 'Photo de profil (URL)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'https://exemple.com/photo.jpg',
                ],
            ])
            ->add('isCertified', CheckboxType::class, [
                'required' => false,
                'label' => 'Certifier cet artiste',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Artist::class,
            'available_users' => [], // par défaut aucun utilisateur
        ]);

        // Forcer que available_users soit toujours un tableau
        $resolver->setAllowedTypes('available_users', ['array']);
    }
}
