<?php

namespace App\Repository;

use App\Entity\Attempt;
use App\Service\StatManager;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Attempt|null find($id, $lockMode = null, $lockVersion = null)
 * @method Attempt|null findOneBy(array $criteria, array $orderBy = null)
 * @method Attempt[]    findAll()
 * @method Attempt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttemptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attempt::class);
    }

    public function getSuccessCount(?\DateTime $date, bool $isVip)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('count(a.id)')
            ->andWhere('a.createdAt = :date')
            ->andWhere('a.isSuccess = true')
            ->join('a.word', 'w');
            if ($isVip) {
                $qb->andWhere('w.isVip = true');
            } else {
                $qb->andWhere('w.isVip is null or w.isVip = 0');
            }
            $qb->setParameter('date', $date->format('Y-m-d'));
            return $qb->getQuery()->getSingleScalarResult();
    }

    public function getAttemptCount(?\DateTime $date, bool $isVip)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('count(a.id)')
            ->andWhere('a.createdAt = :date')
            ->join('a.word', 'w');
        if ($isVip) {
            $qb->andWhere('w.isVip = true');
        } else {
            $qb->andWhere('w.isVip is null');
        }
        $qb->setParameter('date', $date->format('Y-m-d'));
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getFailCount(?\DateTime $date, bool $isVip)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('count(a.id)')
            ->andWhere('a.createdAt = :date')
            ->andWhere('a.isSuccess = false')
            ->andWhere('a.number = ' . StatManager::MAX_ATTEMPT)
            ->join('a.word', 'w');
        if ($isVip) {
            $qb->andWhere('w.isVip = true');
        } else {
            $qb->andWhere('w.isVip is null');
        }
        $qb->setParameter('date', $date->format('Y-m-d'));
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getSuccessByAttempts(?\DateTime $date, bool $isVip, int $attempt)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('count(a.id)')
            ->andWhere('a.createdAt = :date')
            ->andWhere('a.isSuccess = true')
            ->andWhere('a.number = ' . $attempt)
            ->join('a.word', 'w');
        if ($isVip) {
            $qb->andWhere('w.isVip = true');
        } else {
            $qb->andWhere('w.isVip is null');
        }
        $qb->setParameter('date', $date->format('Y-m-d'));
        return $qb->getQuery()->getSingleScalarResult();
    }

    // /**
    //  * @return Attempt[] Returns an array of Attempt objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Attempt
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
