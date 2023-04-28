<?php

namespace App\Form;

use App\Entity\Image;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('mediaTitle', TextType::class, [
                'label' => "Titre du média",
                'attr' => [
                    'placeholder' => "Veuillez renseigner le titre du média"
                ],
                'required' => true
            ])
            ->add('mediaContent', CKEditorType::class, [
                'label' => "Contenu du média",
                'attr' => [
                    'placeholder' => "Veuillez renseigner le contenu du média"
                ],
                'required' => true
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Fichier image',
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
            ->add('caption', TextType::class, [
                'label' => "Description de l'image",
                'attr' => [
                    'placeholder' => "Veuillez donner une courte description de l'image"
                ],
                'required' => true
            ])
            ->add('urlLink', TextType::class, [
                'label' => "Lien du média",
                'attr' => [
                    'placeholder' => "Ajoutez un lien URL au média"
                ],
                'required' => false
            ])
            ->add('urlTitle', TextType::class, [
                'label' => "Texte du lien",
                'attr' => [
                    'placeholder' => "Ajoutez un texte personnalisé pour le lien URL du média"
                ],
                'required' => false
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
            'data_class' => Image::class,
        ]);
    }
}
