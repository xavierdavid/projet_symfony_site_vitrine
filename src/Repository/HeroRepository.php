<?php

namespace App\Repository;

use App\Entity\Hero;
use App\Services\SearchHero;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Hero>
 *
 * @method Hero|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hero|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hero[]    findAll()
 * @method Hero[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HeroRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hero::class);
    }

    /**
     * Permet de configurer une requête personnalisée pour récupérer en base de données les objets Hero en fonction des propriétés de filtre de l'objet SearchHero
     *
     * @param SearchHero $searchHero
     * @return Hero[]
     */
    public function findWithSearchHero(SearchHero $searchHero)
    {
        // Configuration de la requête qui récupère les objets Hero
        $query = $this
            ->createQueryBuilder('h')
            // Sélection des objets Hero  
            ->select('h')
            // Tri des objets Hero par slogan et par ordre décroissant
            ->orderBy('h.slogan', 'DESC');
        // Affinement de la requête si un filtre d'ordre de priorité est présent dans l'objet SearchHero
        if(!empty($searchHero->priorityOrder)) {
            $query = $query
                ->orderBy('h.priorityOrder', 'ASC');
        }
        // Affinement de la requête si un filtre de mot clé $string est présent dans l'objet SearchHero
        if(!empty($searchHero->string)) {
            $query = $query
                ->andWhere('h.slogan LIKE :string')
                ->setParameter('string', "%$searchHero->string%");
        }
        // Retour des résultats de la requête
        return $query->getQuery()->getResult();
    }

//    /**
//     * @return Hero[] Returns an array of Hero objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Hero
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
