<?php

namespace App\Repository;

use App\Entity\Document;
use App\Services\SearchDocument;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<File>
 *
 * @method Document|null find($id, $lockMode = null, $lockVersion = null)
 * @method Document|null findOneBy(array $criteria, array $orderBy = null)
 * @method Document[]    findAll()
 * @method Document[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    public function save(Document $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Document $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Permet de configurer une requête personnalisée pour récupérer en base de données les objets Document en fonction des propriétés de filtre de l'objet SearchDocument
     *
     * @param SearchDocument $searchDocument
     * @return Document[]
     */
    public function findWithSearchDocument(SearchDocument $searchDocument)
    {
        // Configuration de la requête qui récupère les objets Document
        $query = $this
            ->createQueryBuilder('d')
            // Sélection des objets Document  
            ->select('d')
            // Tri des objets Document par nom et par ordre croissant
            ->orderBy('d.name', 'ASC');
        // Affinement de la requête si un filtre de mot clé $string est présent dans l'objet SearchDocument
        if(!empty($searchDocument->string)) {
            $query = $query
                ->andWhere('d.name LIKE :string')
                ->setParameter('string', "%$searchDocument->string%");
        }
        // Retour des résultats de la requête
        return $query->getQuery()->getResult();
    }

//    /**
//     * @return File[] Returns an array of File objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?File
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
