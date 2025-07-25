<?php

namespace App\Repository;

use App\Entity\Payment;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Payment>
 */
class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    public function findByPaymentDatePeriod(DateTimeInterface $start, DateTimeInterface $end): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.payment_date >= :start')
            ->andWhere('p.payment_date < :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getArrayResult();
    }
}
