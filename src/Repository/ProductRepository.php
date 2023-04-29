<?php

namespace App\Repository;

use App\Entity\Product;
use App\Services\SearchProduct;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function save(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Permet de configurer une requête personnalisée pour récupérer en base de données les objets Product en fonction des propriétés de filtre de l'objet SearchProduct
     *
     * @param SearchProduct $searchProduct
     * @return Product[]
     */
    public function findWithSearchProduct(SearchProduct $searchProduct)
    {
        // Configuration de la requête qui récupère les objets Product
        $query = $this
            ->createQueryBuilder('p')
            // Sélection des objets Product  
            ->select('p')
            // Tri des objets Product par date de mise à jour et par ordre décroissant
            ->orderBy('p.updatedAt', 'DESC');
        // Affinement de la requête si un filtre d'ordre de priorité est présent dans l'objet SearchProduct
        if(!empty($searchProduct->priorityOrder)) {
            $query = $query
                ->orderBy('p.priorityOrder', 'ASC');
        }
        // Affinement de la requête si un filtre de mot clé $string est présent dans l'objet SearchProduct
        if(!empty($searchProduct->string)) {
            $query = $query
                ->andWhere('p.name LIKE :string')
                ->setParameter('string', "%$searchProduct->string%");
        }
        // Retour des résultats de la requête
        return $query->getQuery()->getResult();
    }

//    /**
//     * @return Product[] Returns an array of Product objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
