<?php

namespace App\Repository;

use App\Entity\Article;
use App\Services\SearchArticle;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function save(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Permet de configurer une requête personnalisée pour récupérer en base de données les objets Article en fonction des propriétés de filtre de l'objet SearchArticle
     *
     * @param SearchArticle $searchArticle
     * @return Article[]
     */
    public function findWithSearchArticle(SearchArticle $searchArticle)
    {
        // Configuration de la requête qui récupère les objets Article et les objets Category associés
        $query = $this
            ->createQueryBuilder('a')
            // Sélection des objets Category et Article  
            ->select('c', 'a')
            // Jointure entre les objets Article et les objets Category - Récupère par défaut tous les objets Article même ceux non associés à un objet Category (leftJoin) 
            ->leftJoin('a.categories', 'c')
            // Jointure entre les objets Article et les objets User - Récupère par défaut tous les objets Article même ceux non associés à un objet User (leftJoin) 
            ->leftJoin('a.user', 'u')
            // Tri des objets Article par date de mise à jour et par ordre décroissant
            ->orderBy('a.updatedAt', 'DESC');
        // Affinement de la requête si un filtre de catégorie $category est présent dans l'objet SearchArticle
        if(!empty($searchArticle->category)) {
            $query = $query
                ->andWhere('c.id IN (:category)')
                ->setParameter('category', $searchArticle->category);
        }
        // Affinement de la requête si un filtre d'ordre de priorité est présent dans l'objet SearchArticle
        if(!empty($searchArticle->priorityOrder)) {
            $query = $query
                ->orderBy('a.priorityOrder', 'ASC');
        }
        // Affinement de la requête si un filtre de mot clé $string est présent dans l'objet SearchArticle
        if(!empty($searchArticle->string)) {
            $query = $query
                ->andWhere('a.title LIKE :string OR u.firstname LIKE :string OR u.lastname LIKE :string')
                ->setParameter('string', "%$searchArticle->string%");
        }
        // Retour des résultats de la requête
        return $query->getQuery()->getResult();
    }

//    /**
//     * @return Article[] Returns an array of Article objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Article
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
