<?php

namespace App\Application\Purchase\Repository;

use Doctrine\Persistence\ManagerRegistry;
use App\Application\Purchase\Entity\PurchaseProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<PurchaseProduct>
 *
 * @method PurchaseProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method PurchaseProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method PurchaseProduct[]    findAll()
 * @method PurchaseProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurchaseProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurchaseProduct::class);
    }

    public function add(PurchaseProduct $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PurchaseProduct $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return PurchaseProduct[] Returns an array of PurchaseProduct objects
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

//    public function findOneBySomeField($value): ?PurchaseProduct
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
