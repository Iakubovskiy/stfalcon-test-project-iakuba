<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Property;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Property>
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly EntityManagerInterface $entityManager
    )
    {
        parent::__construct($registry, Property::class);
    }

    //    /**
    //     * @return Property[] Returns an array of Property objects
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

    //    public function findOneBySomeField($value): ?Property
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function getAllPropertiesForUsers(array $visibleStatuses,int $offset, int $limit):array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(Property::class, 'p')
            ->where('p.status IN (:statuses)')
            ->setParameter('statuses', $visibleStatuses)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery();

        $paginator = new Paginator($query);
        return [
            'result' => iterator_to_array($paginator),
            'total' => $paginator->count(),
            'offset' => $offset,
            'limit' => $limit,
        ];
    }

    public function getAllProperties(int $offset, int $limit): array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(Property::class, 'p')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery();

        $paginator = new Paginator($query);
        return [
            'result' => iterator_to_array($paginator),
            'total' => $paginator->count(),
            'offset' => $offset,
            'limit' => $limit,
        ];
    }


}
