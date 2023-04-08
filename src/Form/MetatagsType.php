<?php

namespace App\Form;

use App\Entity\Metatags;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MetatagsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => "Balise de titre de la page d'accueil du site",
                'attr' => [
                    'placeholder' => "Veuillez renseigner le contenu de la balise de titre de la page d'accueil du site"
                ]
            ])
            ->add('metaDescription', TextareaType::class, [
                'label' => "Balise metadescription du site",
                'attr' => [
                    'placeholder' => "Veuillez renseigner le contenu de la balise metadescription du site"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Metatags::class,
        ]);
    }
}
