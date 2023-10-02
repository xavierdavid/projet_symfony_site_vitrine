<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UpdatePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class,[
                'disabled' => true,
                'label' => "Mon adresse email"
            ])
            ->add('firstname', TextType::class, [
                'disabled' => true,
                'label' => "Mon prénom"
            ])
            ->add('lastname', TextType::class, [
                'disabled' => true,
                'label' => "Mon nom"
            ])
            ->add('old_password', PasswordType::class, [
                'mapped' => false,
                'label' => "Mon mot de passe actuel",
                'attr' => [
                    'placeholder' => "Saisir votre mot de passe actuel"
                ]
            ])
            ->add('new_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => "Les deux mots de passe doivent être identiques !",
                'options' => [
                    'attr' => ['class' => 'password-field']
                ],
                'required' => true,
                'first_options' => [
                    'label' => "Nouveau mot de passe",
                    'attr' => [
                        'placeholder' => "Saisir un nouveau mot de passe"
                    ],
                ],
                'second_options' => [
                    'label' => "Confirmez votre nouveau mot de passe",
                    'attr' => [
                        'placeholder' => "Confirmer votre nouveau mot de passe"
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
