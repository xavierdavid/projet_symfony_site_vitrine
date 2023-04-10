<?php

namespace App\Form;

use App\Entity\Metatag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MetatagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pageName', TextType::class, [
                'label' => "Nom de la page",
                'disabled' => true
            ])
            ->add('title', TextType::class, [
                'label' => "Titre de la page",
                'attr' => [
                    'placeholder' => "Veuillez renseigner le titre de la page"
                ]
            ])
            ->add('metaDescription', TextareaType::class, [
                'label' => "Contenu de la balise metadescription de la page",
                'attr' => [
                    'placeholder' => "Veuillez renseigner le contenu de la balise metadescription de la page"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Metatag::class,
        ]);
    }
}
