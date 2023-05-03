<?php

namespace App\Repository;

use App\Entity\Partner;
use App\Services\SearchPartner;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Partner>
 *
 * @method Partner|null find($id, $lockMode = null, $lockVersion = null)
 * @method Partner|null findOneBy(array $criteria, array $orderBy = null)
 * @method Partner[]    findAll()
 * @method Partner[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PartnerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Partner::class);
    }

    public function save(Partner $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Partner $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Permet de configurer une requête personnalisée pour récupérer en base de données les objets Partner en fonction des propriétés de filtre de l'objet SearchPartner
     *
     * @param SearchPartner $searchPartner
     * @return Partner[]
     */
    public function findWithSearchPartner(SearchPartner $searchPartner)
    {
        // Configuration de la requête qui récupère les objets Partner
        $query = $this
            ->createQueryBuilder('p')
            // Sélection des objets Partner  
            ->select('p')
            // Tri des objets PArtner par date de mise à jour et par ordre décroissant
            ->orderBy('p.updatedAt', 'DESC');
        // Affinement de la requête si un filtre d'ordre de priorité est présent dans l'objet SearchPartner
        if(!empty($searchPartner->priorityOrder)) {
            $query = $query
                ->orderBy('p.priorityOrder', 'ASC');
        }
        // Affinement de la requête si un filtre de mot clé $string est présent dans l'objet SearchPartner
        if(!empty($searchPartner->string)) {
            $query = $query
                ->andWhere('p.name LIKE :string')
                ->setParameter('string', "%$searchPartner->string%");
        }
        // Retour des résultats de la requête
        return $query->getQuery()->getResult();
    }

//    /**
//     * @return Partner[] Returns an array of Partner objects
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

//    public function findOneBySomeField($value): ?Partner
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
