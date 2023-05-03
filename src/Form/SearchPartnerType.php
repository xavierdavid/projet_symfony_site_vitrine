<?php 

namespace App\Form;

use App\Services\SearchPartner;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class SearchPartnerType extends AbstractType
{
  /**
   * Configure les champs du formulaire de recherche d'objets Partner
   *
   * @param FormBuilderInterface $builder
   * @param array $options
   * @return void
   */
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('string', TextType::class, [
        'label' => false,
        'required' => false,
        'attr' => [
          'placeholder' => "Rechercher un partenaire ..."
        ]
      ])
      ->add('priorityOrder', CheckboxType::class, [
        'label' => "Trier par ordre de priorité de publication",
        'required' => false,
      ]);
  }

  /**
   * Configure les options du formulaire de recherche d'objets Partner
   *
   * @param OptionsResolver $resolver
   * @return void
   */
  public function configurationOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => SearchPartner::class,
      'method' => 'GET',
      'csrf_protection' => false,
      'allow_extra_fields' => true
    ]);
  }

  /**
   * Supprime dans l'url l'affichage du tableau des résultats préfixé du nom de la classe Partner
   *
   * @return Response
   */
  public function getBlockPrefix()
  {
    return '';
  }
}