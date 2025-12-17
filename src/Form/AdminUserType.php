<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class AdminUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['is_edit'];
        $isRequired = !$isEdit;

        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email *',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'exemple@email.com',
                    'autocomplete' => 'email'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'L\'email est obligatoire.']),
                ],
            ])
            ->add('username', TextType::class, [
                'label' => 'Nom d\'utilisateur *',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Nom d\'utilisateur',
                    'autocomplete' => 'username'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le nom d\'utilisateur est obligatoire.']),
                    new Length([
                        'min' => 3,
                        'max' => 100,
                        'minMessage' => 'Le nom d\'utilisateur doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le nom d\'utilisateur ne peut pas dépasser {{ limit }} caractères.'
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9_\-]+$/',
                        'message' => 'Le nom d\'utilisateur ne peut contenir que des lettres, chiffres, tirets ou underscores.'
                    ])
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'first_options' => [
                    'label' => 'Mot de passe' . ($isRequired ? ' *' : ''),
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => $isEdit ? 'Laisser vide pour ne pas modifier' : 'Minimum 12 caractères',
                        'autocomplete' => 'new-password'
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe' . ($isRequired ? ' *' : ''),
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Répétez le mot de passe',
                        'autocomplete' => 'new-password'
                    ]
                ],
                'required' => $isRequired,
                'mapped' => true,
                'constraints' => $isRequired ? [
                    new NotBlank(['message' => 'Le mot de passe est obligatoire.']),
                    new Length(['min' => 12, 'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères.', 'max' => 4096]),
                    new Regex([
                        'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).+$/',
                        'message' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial (@$!%*?&).'
                    ])
                ] : []
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôles *',
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Artiste' => 'ROLE_ARTIST',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'multiple' => true,
                'expanded' => true,
                'required' => true,
                'help' => 'Sélectionnez un ou plusieurs rôles. Le rôle "Utilisateur" est automatiquement inclus.'
            ])
            ->add('isVerified', CheckboxType::class, [
                'label' => 'Email vérifié',
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
            'is_edit' => false,
            'validation_groups' => function ($form) {
                $groups = ['Default'];
                $data = $form->getData();

                if ($data && $data->isNew()) {
                    $groups[] = 'registration';
                } elseif ($data && $data->getPlainPassword()) {
                    $groups[] = 'password_change';
                }

                return $groups;
            }
        ]);

        $resolver->setAllowedTypes('is_edit', 'bool');
    }
}
