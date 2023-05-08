<?php

namespace App\Repository;

use App\Entity\Administrator;
use App\Services\SearchAdministrator;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Administrator>
 *
 * @method Administrator|null find($id, $lockMode = null, $lockVersion = null)
 * @method Administrator|null findOneBy(array $criteria, array $orderBy = null)
 * @method Administrator[]    findAll()
 * @method Administrator[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdministratorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Administrator::class);
    }

    public function save(Administrator $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Administrator $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Permet de configurer une requête personnalisée pour récupérer en base de données les objets Administrator en fonction des propriétés de filtre de l'objet SearchAdministrator
     *
     * @param SearchAdministrator $searchAdministrator
     * @return Administrator[]
     */
    public function findWithSearchAdministrator(SearchAdministrator $searchAdministrator)
    {
        // Configuration de la requête qui récupère les objets Administrator
        $query = $this
            ->createQueryBuilder('a')
            // Sélection des objets Administrator  
            ->select('a')
            // Tri des objets Administrator par date de mise à jour et par ordre décroissant
            ->orderBy('a.updatedAt', 'DESC');
        // Affinement de la requête si un filtre d'ordre de priorité est présent dans l'objet SearchAdministrator
        if(!empty($searchAdministrator->priorityOrder)) {
            $query = $query
                ->orderBy('a.priorityOrder', 'ASC');
        }
        // Affinement de la requête si un filtre de mot clé $string est présent dans l'objet SearchAdministrator
        if(!empty($searchAdministrator->string)) {
            $query = $query
                ->andWhere('a.firstname LIKE :string OR a.lastname LIKE :string')
                ->setParameter('string', "%$searchAdministrator->string%");
        }
        // Retour des résultats de la requête
        return $query->getQuery()->getResult();
    }

//    /**
//     * @return Administrator[] Returns an array of Administrator objects
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

//    public function findOneBySomeField($value): ?Administrator
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
