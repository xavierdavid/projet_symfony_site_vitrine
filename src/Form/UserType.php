<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => "Prénom de l'utilisateur",
                'attr'  => [
                    'placeholder' => "Veuillez saisir le prénom de l'utilisateur"
                ],
                'required' => true,
            ])
            ->add('lastname', TextType::class, [
                'label' => "Nom de l'utilisateur",
                'attr'  => [
                    'placeholder' => "Veuillez saisir le nom de l'utilisateur"
                ],
                'required' => true,
            ]) 
            ->add('email', EmailType::class,[
                'label' => 'Email',
                'attr' => [
                    'placeholder' => "Merci de saisir l'adresse email de l'utilisateur"
                ],
                'required' => true
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => "Les deux mots de passe doivent être identiques !",
                'options' => [
                    'attr' => ['class' => 'password-field']
                ],
                'required' => true,
                'first_options' => [
                    'label' => "Mot de passe",
                    'attr' => [
                        'placeholder' => "Veuillez saisir un mot de passe"
                    ],
                ],
                'second_options' => [
                    'label' => "Confirmez le mot de passe",
                    'attr' => [
                        'placeholder' => "Veuillez confirmer le mot de passe"
                    ]
                ],
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                            'message' => "Merci d'entrer un mot de passe",
                    ]),
                    new Length([
                            'min' => 6,
                            'minMessage' => "Votre mot de passe doit comporter au minimum 6 caractères",
                            // max length allowed by Symfony for security reasons
                            'max' => 4096
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
