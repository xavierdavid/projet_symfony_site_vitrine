<?php 

namespace App\Form;

use App\Entity\Category;
use App\Services\SearchArticle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class SearchArticleSimpleType extends AbstractType
{
  /**
   * Configure les champs du formulaire de recherche d'objets Article
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
          'placeholder' => "Rechercher un article ..."
        ]
      ])
      ->add('category', EntityType::class, [
        'label' => false,
        'required' => false,
        'class' => Category::class,
        'choice_label' => 'name',
        'multiple' => true,
        'expanded' => true
      ])
    ;
  }

  /**
   * Configure les options du formulaire de recherche d'objets Article
   *
   * @param OptionsResolver $resolver
   * @return void
   */
  public function configurationOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => SearchArticle::class,
      'method' => 'GET',
      'csrf_protection' => false,
      'allow_extra_fields' => true
    ]);
  }

  /**
   * Supprime dans l'url l'affichage du tableau des résultats préfixé du nom de la classe Article
   *
   * @return Response
   */
  public function getBlockPrefix()
  {
    return '';
  }
}