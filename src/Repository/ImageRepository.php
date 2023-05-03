<?php

namespace App\Repository;

use App\Entity\Image;
use App\Services\SearchImage;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Image>
 *
 * @method Image|null find($id, $lockMode = null, $lockVersion = null)
 * @method Image|null findOneBy(array $criteria, array $orderBy = null)
 * @method Image[]    findAll()
 * @method Image[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Image::class);
    }

    public function save(Image $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Image $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Permet de configurer une requête personnalisée pour récupérer en base de données les objets Image en fonction des propriétés de filtre de l'objet SearchImage
     *
     * @param SearchImage $searchImage
     * @return Image[]
     */
    public function findWithSearchImage(SearchImage $searchImage)
    {
        // Configuration de la requête qui récupère les objets Image
        $query = $this
            ->createQueryBuilder('i')
            // Sélection des objets Image  
            ->select('i')
            // Tri des objets Image par nom et par ordre croissant
            ->orderBy('i.mediaTitle', 'ASC');
        // Affinement de la requête si un filtre d'ordre de priorité est présent dans l'objet SearchImage
        if(!empty($searchImage->priorityOrder)) {
            $query = $query
                ->orderBy('i.priorityOrder', 'ASC');
        }
        // Affinement de la requête si un filtre de mot clé $string est présent dans l'objet SearchImage
        if(!empty($searchImage->string)) {
            $query = $query
                ->andWhere('i.mediaTitle LIKE :string')
                ->setParameter('string', "%$searchImage->string%");
        }
        // Retour des résultats de la requête
        return $query->getQuery()->getResult();
    }

//    /**
//     * @return Image[] Returns an array of Image objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Image
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
