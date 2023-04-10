<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'attr'=> [
                    'placeholder' => "Veuillez renseigner votre prénom"
                ],
                'required' => true
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'attr'=> [
                    'placeholder' => "Veuillez renseigner votre nom"
                ],
                'required' => true
            ])
            ->add('organization', TextType::class, [
                'label' => 'Organisation',
                'attr'=> [
                    'placeholder' => "Veuillez renseigner le nom de votre organisation (facultatif)"
                ],
                'required' => false
            ])
            ->add('email', EmailType::class,[
                'label' => 'Email',
                'attr' => [
                    'placeholder' => "Merci de saisir votre adresse email"
                ],
                'required' => true
            ])
            ->add('subject', TextType::class, [
                'label' => 'Sujet',
                'attr'=> [
                    'placeholder' => "Veuillez renseigner le sujet de votre message"
                ],
                'required' => false
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Votre message',
                'attr'=> [
                    'placeholder' => "Veuillez rédiger votre message"
                ],
                'required' => true
            ]) 
            ->add('isRgpd', CheckboxType::class, [
                'label' => 'Consentement RGPD',
                'attr'=> [
                    'placeholder' => "J'autorise ce site à conserver mes données personnelles transmises via ce formulaire. Aucune exploitation commerciale ne sera faite des données conservées."
                ],
                'required' => true
            ]) 
            ->add('captcha', Recaptcha3Type::class, [
                'constraints' => new Recaptcha3(),
                'action_name' => 'contact',
            ]) 
 
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
