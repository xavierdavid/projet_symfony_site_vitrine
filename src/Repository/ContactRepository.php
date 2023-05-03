<?php

namespace App\Repository;

use App\Entity\Contact;
use App\Services\SearchContact;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Contact>
 *
 * @method Contact|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contact|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contact[]    findAll()
 * @method Contact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }

    public function save(Contact $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Contact $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Permet de configurer une requête personnalisée pour récupérer en base de données les objets Contact en fonction des propriétés de filtre de l'objet SearchContact
     *
     * @param SearchContact $searchContact
     * @return Contact[]
     */
    public function findWithSearchContact(SearchContact $searchContact)
    {
        // Configuration de la requête qui récupère les objets Contact
        $query = $this
            ->createQueryBuilder('c')
            // Sélection des objets Contact  
            ->select('c')
            // Tri des objets Contact par date de création et par ordre décroissant
            ->orderBy('c.createdAt', 'DESC');
        // Affinement de la requête si un filtre 'email' est présent dans l'objet SearchContact
        if(!empty($searchContact->email)) {
            $query = $query
                ->orderBy('c.email', 'ASC');
        }
        // Affinement de la requête si un filtre de mot clé $string est présent dans l'objet SearchContact
        if(!empty($searchContact->string)) {
            $query = $query
                ->andWhere('c.email LIKE :string OR c.organization LIKE :string OR c.subject LIKE :string OR c.message LIKE :string')
                ->setParameter('string', "%$searchContact->string%");
        }
        // Retour des résultats de la requête
        return $query->getQuery()->getResult();
    }

//    /**
//     * @return Contact[] Returns an array of Contact objects
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

//    public function findOneBySomeField($value): ?Contact
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
