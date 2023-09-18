<?php

namespace App\Form;

use App\Entity\Image;
use App\Entity\Product;
use App\Entity\Document;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => "Nom du service",
                'attr' => [
                    'placeholder' => "Veuillez indiquer le nom du service"
                ], 
                'required' => true
            ])
            ->add('coverImage', FileType::class, [
                'label' => 'Image de couverture',
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
                'label' => "Texte d'introduction",
                'attr' => [
                    'placeholder' => "Veuillez rédiger un court texte de présentation du service"
                ],
                'required' => true
            ])
            ->add('description', TextareaType::class, [
                'label' => "Contenu du service",
                'attr' => [
                    'placeholder' => "Veuillez rédiger le contenu de l'article"
                ],
                'required' => true
            ])
            ->add('images', EntityType::class, [
                'label' => "Média(s) associé(s) au service",
                'class' => Image::class,
                'multiple' => true,
                'expanded' => true
            ])
            ->add('documents', EntityType::class, [
                'label' => "Document(s) associé(s) au service",
                'class' => Document::class,
                'multiple' => true,
                'expanded' => true
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
            'data_class' => Product::class,
        ]);
    }
}
