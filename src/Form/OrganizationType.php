<?php

namespace App\Form;

use App\Entity\Organization;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class OrganizationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('organizationName', TextType::class, [
                'label' => "Nom de l'organisation",
                'attr' => [
                    'placeholder' => "Veuillez renseigner le nom de votre organisation"
                ],
                'required' => true,
            ])
            ->add('siteTitle', TextType::class, [
                'label' => "Nom du site",
                'attr' => [
                    'placeholder' => "Veuillez renseigner le nom de votre site internet"
                ],
                'required' => true,
            ])
            ->add('address', TextType::class, [
                'label' => "Adresse",
                'attr' => [
                    'placeholder' => "Veuillez saisir votre adresse postale"
                ],
                'required' => true,
            ])
            ->add('postal', TextType::class, [
                'label' => "Code postal",
                'attr'  => [
                    'placeholder' => "Veuillez saisir votre code postal"
                ],
                'required' => true,
            ])
            ->add('city', TextType::class, [
                'label' => "Ville",
                'attr'  => [
                    'placeholder' => "Veuillez saisir votre ville"
                ],
                'required' => true,
            ])
            ->add('country', CountryType::class, [
                'label' => "Pays",
                'attr'  => [
                    'placeholder' => "Veuillez sélectionner votre pays"
                ],
                'required' => true,
            ])
            ->add('phone', TelType::class, [
                'label' => "Téléphone",
                'attr'  => [
                    'placeholder' => "Veuillez saisir votre numéro de téléphone"
                ],
                'required' => true,
            ])
            ->add('facebook', TextType::class, [
                'label' => "Facebook",
                'attr'  => [
                    'placeholder' => "Veuillez saisir l'identifiant de votre compte Facebook"
                ],
                'required' => false,
            ])
            ->add('instagram', TextType::class, [
                'label' => "Instagram",
                'attr'  => [
                    'placeholder' => "Veuillez saisir l'identifiant de votre compte Instagram"
                ],
                'required' => false,
            ])
            ->add('twitter', TextType::class, [
                'label' => "Twitter",
                'attr'  => [
                    'placeholder' => "Veuillez saisir l'identifiant de votre compte Twitter"
                ],
                'required' => false,
            ])
            ->add('shortDescription', TextareaType::class, [
                'label' => "Courte description",
                'attr'  => [
                    'placeholder' => "Décrivez en quelques mots votre organisation"
                ],
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => "Description",
                'attr'  => [
                    'placeholder' => "Présentez votre organisation"
                ],
                'required' => true,
            ])
            ->add('logo', FileType::class, [
                'label' => "Logo de l'organisation",
                'attr' => [
                    'placeholder' => "Veuillez choisir un fichier image"
                ],
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypesMessage' => "Veuillez téléverser une image au format JPEG ou PNG !"
                    ])
                ],
                'data_class' => null,
                'mapped' => false
            ])
            ->add('administratorFirstname', TextType::class, [
                'label' => "Prénom du responsable",
                'attr'  => [
                    'placeholder' => "Veuillez saisir le prénom du responsable de l'organisation"
                ],
                'required' => true,
            ])
            ->add('administratorLastname', TextType::class, [
                'label' => "Nom du responsable",
                'attr'  => [
                    'placeholder' => "Veuillez saisir le nom du responsable de l'organisation"
                ],
                'required' => true,
            ]) 
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Organization::class,
        ]);
    }
}
