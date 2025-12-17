<?php
// src/Form/AdminArtistType.php
namespace App\Form;

use App\Entity\Artist;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminArtistType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $availableUsers = $options['available_users'] ?? [];

        $builder
            ->add('name', null, [
                'label' => 'Nom de l\'artiste',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Nom complet de l\'artiste',
                ],
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choices' => $availableUsers, // utilisateurs disponibles seulement
                'choice_label' => 'email',
                'placeholder' => 'Sélectionner un utilisateur',
                'required' => true,
                'label' => 'Utilisateur associé',
            ])
            ->add('ecoTechnique', null, [
                'label' => 'Technique écologique',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex: Recyclage, Upcycling...',
                ],
            ])
            ->add('bio', null, [
                'label' => 'Biographie',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Décrivez le parcours, les techniques et l\'approche écologique de l\'artiste...',
                    'rows' => 6,
                ],
            ])
            ->add('profilePicture', null, [
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
            'available_users' => [],
        ]);

        // Forcer que available_users soit toujours un tableau
        $resolver->setAllowedTypes('available_users', ['array']);
    }
}
