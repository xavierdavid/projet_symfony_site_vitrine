<?php

namespace App\Form;

use App\Entity\Document;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\File;

class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => "Nom du document",
                'attr' => [
                    'placeholder' => "Veuillez renseigner le nom du document"
                ],
                'required' => true
            ])
            ->add('documentFile', FileType::class, [
                'label' => 'Fichier PDF',
                'attr' => [
                    'placeholder' => "Veuillez choisir un document PDF"
                ],
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => "Veuillez téléverser un document PDF valide SVP !"
                    ])
                ],
                'data_class' => null,
                'mapped' => false
            ])
            ->add('caption', TextType::class, [
                'label' => "Description du document",
                'attr' => [
                    'placeholder' => "Veuillez donner une courte description du document"
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Document::class,
        ]);
    }
}
