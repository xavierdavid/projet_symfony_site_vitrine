<?php

namespace App\Repository;

use App\Entity\Collaborator;
use App\Services\SearchCollaborator;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Collaborator>
 *
 * @method Collaborator|null find($id, $lockMode = null, $lockVersion = null)
 * @method Collaborator|null findOneBy(array $criteria, array $orderBy = null)
 * @method Collaborator[]    findAll()
 * @method Collaborator[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CollaboratorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Collaborator::class);
    }

    public function save(Collaborator $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Collaborator $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Permet de configurer une requête personnalisée pour récupérer en base de données les objets Collaborator en fonction des propriétés de filtre de l'objet SearchCollaborator
     *
     * @param SearchCollaborator $searchCollaborator
     * @return Collaborator[]
     */
    public function findWithSearchCollaborator(SearchCollaborator $searchCollaborator)
    {
        // Configuration de la requête qui récupère les objets Collaborator
        $query = $this
            ->createQueryBuilder('c')
            // Sélection des objets Collaborator  
            ->select('c')
            // Tri des objets Collaborator par date de mise à jour et par ordre décroissant
            ->orderBy('c.updatedAt', 'DESC');
        // Affinement de la requête si un filtre d'ordre de priorité est présent dans l'objet SearchCollaborator
        if(!empty($searchCollaborator->priorityOrder)) {
            $query = $query
                ->orderBy('c.priorityOrder', 'ASC');
        }
        // Affinement de la requête si un filtre de mot clé $string est présent dans l'objet SearchCollaborator
        if(!empty($searchCollaborator->string)) {
            $query = $query
                ->andWhere('c.firstname LIKE :string OR c.lastname LIKE :string')
                ->setParameter('string', "%$searchCollaborator->string%");
        }
        // Retour des résultats de la requête
        return $query->getQuery()->getResult();
    }

//    /**
//     * @return Collaborator[] Returns an array of Collaborator objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Collaborator
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
