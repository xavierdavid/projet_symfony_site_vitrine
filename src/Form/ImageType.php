<?php

namespace App\Form;

use App\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => "Nom de l'image",
                'attr' => [
                    'placeholder' => "Veuillez renseigner le nom de l'image"
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
