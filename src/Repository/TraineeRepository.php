<?php

namespace App\Repository;

use App\Entity\Trainee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Trainee>
 */
class TraineeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trainee::class);
    }

    public function getAbsenceStatistics(): array
    {
        return $this->createQueryBuilder('t')
            ->select('t.id AS traineeId')
            ->addSelect('t.firstName AS firstName')
            ->addSelect('t.lastName AS lastName')
            ->addSelect('COUNT(a.id) AS totalAbsences')
            ->addSelect(
                'SUM(
                CASE
                    WHEN a.reason = :unexcused
                    THEN 1
                    ELSE 0
                END
            ) AS unexcusedAbsences'
            )
            ->leftJoin('t.absences', 'a')
            ->setParameter('unexcused', 'unexcused')
            ->groupBy('t.id')
            ->addGroupBy('t.firstName')
            ->addGroupBy('t.lastName')
            ->orderBy('totalAbsences', 'DESC')
            ->addOrderBy('t.lastName', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }
    //    /**
    //     * @return Trainee[] Returns an array of Trainee objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Trainee
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
