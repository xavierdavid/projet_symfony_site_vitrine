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
                    'placeholder' => "John"
                ],
                'required' => true
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'attr'=> [
                    'placeholder' => "DOE"
                ],
                'required' => true
            ])
            ->add('organization', TextType::class, [
                'label' => 'Organisation',
                'attr'=> [
                    'placeholder' => "Nom de votre organisation"
                ],
                'required' => false
            ])
            ->add('email', EmailType::class,[
                'label' => 'Email',
                'attr' => [
                    'placeholder' => "johndoe@exemple.com"
                ],
                'required' => true
            ])
            ->add('subject', TextType::class, [
                'label' => 'Sujet',
                'attr'=> [
                    'placeholder' => "Demande d'informations"
                ],
                'required' => false
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Votre message',
                'attr'=> [
                    'placeholder' => "Votre message"
                ],
                'required' => true
            ]) 
            ->add('isRgpd', CheckboxType::class, [
                'label' => 'Consentement RGPD',
                'help'=> "Vous acceptez que les informations transmises soient conservées et utilisées conformément à notre politique de protection des données.",
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
