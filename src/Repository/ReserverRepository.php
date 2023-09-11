<?php

namespace App\Repository;

use App\Entity\Reserver;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reserver>
 *
 * @method Reserver|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reserver|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reserver[]    findAll()
 * @method Reserver[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReserverRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reserver::class);
    }

    public function findAvailableChambres(array $options, \DateTimeImmutable $dateEntree, \DateTimeImmutable $dateSortie)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->leftJoin('c.reservations', 'r')
            ->andWhere('r.dateEntree > :dateSortie OR r.dateSortie < :dateEntree')
            ->setParameter('dateEntree', $dateEntree)
            ->setParameter('dateSortie', $dateSortie);

        foreach ($options as $option => $value) {
            if ($value) {
                $qb->andWhere("c.$option = :$option")
                    ->setParameter($option, $value);
            }
        }

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return Reserver[] Returns an array of Reserver objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Reserver
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
