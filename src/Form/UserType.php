<?php
// src/Form/UserType.php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isNew = $options['is_new'] ?? false;

        $builder
            ->add('username', TextType::class, [
                'label' => 'Nom d\'utilisateur',
                'attr' => [
                    'placeholder' => 'ex: aziz_benabdallah',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le nom d\'utilisateur est obligatoire']),
                    new Length([
                        'min' => 3,
                        'max' => 100,
                        'minMessage' => 'Le nom d\'utilisateur doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le nom d\'utilisateur ne peut pas dépasser {{ limit }} caractères'
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9_\-]+$/',
                        'message' => 'Le nom d\'utilisateur ne peut contenir que des lettres, chiffres, tirets ou underscores'
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
                'attr' => [
                    'placeholder' => 'ex: utilisateur@domaine.com',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'L\'email est obligatoire'])
                ]
            ]);

        // Configuration différente pour création vs édition
        if ($isNew) {
            // Création : mot de passe obligatoire
            $builder->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'placeholder' => 'Minimum 12 caractères',
                        'class' => 'form-control password-input',
                        'autocomplete' => 'new-password'
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                    'attr' => [
                        'placeholder' => 'Répétez le mot de passe',
                        'class' => 'form-control confirm-password-input',
                        'autocomplete' => 'new-password'
                    ]
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le mot de passe est obligatoire']),
                    new Length([
                        'min' => 12,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères'
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
                        'message' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial (@$!%*?&)'
                    ])
                ]
            ]);
            } // Dans UserType.php, modifie la partie mot de passe pour l'édition :
               else {
                  // Édition : mot de passe optionnel
                  $builder->add('plainPassword', RepeatedType::class, [
                      'type' => PasswordType::class,
                      'invalid_message' => 'Les mots de passe doivent correspondre.',
                      'first_options' => [
                          'label' => 'Nouveau mot de passe',
                          'attr' => [
                              'placeholder' => 'Laisser vide pour ne pas changer',
                              'class' => 'form-control password-input',
                              'autocomplete' => 'new-password'
                          ]
                      ],
                      'second_options' => [
                          'label' => 'Confirmer le mot de passe',
                          'attr' => [
                              'placeholder' => 'Répétez le nouveau mot de passe',
                              'class' => 'form-control confirm-password-input',
                              'autocomplete' => 'new-password'
                          ]
                      ],
                      'required' => false,
                      'mapped' => false, // IMPORTANT: doit être false car c'est un champ custom
                      'constraints' => [
                          new Length([
                              'min' => 12,
                              'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères',
                              'groups' => ['password_change']
                          ]),
                          new Regex([
                              'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
                              'message' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial (@$!%*?&)',
                              'groups' => ['password_change']
                          ])
                      ]
                  ]);
              }

            $builder
                ->add('roles', ChoiceType::class, [
                    'label' => 'Rôles',
                    'choices' => [
                        'Administrateur' => 'ROLE_ADMIN',
                        'Artiste' => 'ROLE_ARTIST',
                    ],
                    'multiple' => true,
                    'expanded' => true,
                    'attr' => ['class' => 'form-check-input'],
                    'label_attr' => ['class' => 'form-check-label'],
                    'help' => 'Le rôle "ROLE_USER" est automatiquement attribué.'
                ])
                ->add('isVerified', CheckboxType::class, [
                    'label' => 'Compte vérifié',
                    'required' => false,
                    'attr' => ['class' => 'form-check-input'],
                    'label_attr' => ['class' => 'form-check-label']
                ])
                ->add('isActive', CheckboxType::class, [
                    'label' => 'Compte actif',
                    'required' => false,
                    'attr' => ['class' => 'form-check-input'],
                'label_attr' => ['class' => 'form-check-label']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_new' => false,
        ]);

        $resolver->setAllowedTypes('is_new', 'bool');
    }
}