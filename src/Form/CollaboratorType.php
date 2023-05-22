<?php

namespace App\Form;

use App\Entity\Collaborator;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CollaboratorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => "Prénom du collaborateur",
                'attr' => [
                    'placeholder' => "Veuillez indiquer le prénom du collaborateur"
                ], 
                'required' => true
            ])
            ->add('lastName', TextType::class, [
            'label' => "Nom du collaborateur",
            'attr' => [
                'placeholder' => "Veuillez indiquer le nom du collaborateur"
            ], 
            'required' => true
        ])
            ->add('coverImage', FileType::class, [
                'label' => 'Photo de profil',
                'attr' => [
                    'placeholder' => "Veuillez choisir un fichier image JPEG ou PNG"
                ],
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => "Veuillez téléverser une image au format valide SVP !"
                    ])
                ],
                'data_class' => null,
                'mapped' => false
            ])
            ->add('shortDescription', TextareaType::class, [
                'label' => "Fonction",
                'attr' => [
                    'placeholder' => "Veuillez indiquer la fonction du collaborateur"
                ],
                'required' => true
            ])
            ->add('description', CKEditorType::class, [
                'label' => "Description du profil",
                'attr' => [
                    'placeholder' => "Veuillez décrire le profil du collaborateur"
                ],
                'required' => true
            ])
            ->add('priorityOrder', IntegerType::class, [
                'label' => "Ordre de priorité de publication",
                'attr' => [
                    'placeholder' =>"Entrez un nombre entier pour définir l'ordre de priorité ascendant"
                ],
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Collaborator::class,
        ]);
    }
}
