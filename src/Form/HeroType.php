<?php

namespace App\Form;

use App\Entity\Hero;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class HeroType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('slogan', TextType::class, [
                'label' => "Slogan principal du site",
                'attr' => [
                    'placeholder' => "Veuillez indiquer le slogan du site"
                ], 
                'required' => true
            ])
            ->add('content', TextareaType::class, [
                'label' => "Texte d'accueil du site",
                'attr' => [
                    'placeholder' => "Veuillez rédiger le texte dd'accueil"
                ],
                'required' => true
            ])
            ->add('firstButtonTitle', TextType::class, [
                'label' => "Texte du premier bouton",
                'attr' => [
                    'placeholder' => "Veuillez saisir le texte du premier bouton"
                ], 
                'required' => true
            ])
            ->add('firstButtonUrl', TextType::class, [
                'label' => "Url du premier bouton",
                'attr' => [
                    'placeholder' => "Veuillez saisir l'url' du premier bouton"
                ], 
                'required' => true
            ])
            ->add('secondButtonTitle', TextType::class, [
                'label' => "Texte du second bouton",
                'attr' => [
                    'placeholder' => "Veuillez saisir le texte du second bouton"
                ], 
                'required' => true
            ])
            ->add('secondButtonUrl', TextType::class, [
                'label' => "Url du second bouton",
                'attr' => [
                    'placeholder' => "Veuillez saisir l'url' du second bouton"
                ], 
                'required' => true
            ])
            ->add('masterImage', FileType::class, [
                'label' => 'Image principale du site',
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
            ->add('masterImageDescription', TextType::class, [
                'label' => "Description de l'image principale du site",
                'attr' => [
                    'placeholder' => "Veuillez décrire l'image"
                ], 
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Hero::class,
        ]);
    }
}
