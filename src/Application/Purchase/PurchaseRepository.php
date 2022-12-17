<?php

namespace App\Application\Purchase;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Purchase>
 *
 * @method Purchase|null find($id, $lockMode = null, $lockVersion = null)
 * @method Purchase|null findOneBy(array $criteria, array $orderBy = null)
 * @method Purchase[]    findAll()
 * @method Purchase[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurchaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Purchase::class);
    }

    public function add(Purchase $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Purchase $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

	/**
	 * @param int[] $ids
	 * @return int
	 * @throws \Doctrine\ORM\NoResultException
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function countPurchaseByProducts(array $ids): int
	{
		return $this->createQueryBuilder('p')
			->select('count(p.id)')
			->where('p.product in (:ids)')
			->setParameter('ids', $ids)
			->getQuery()
			->getSingleScalarResult();
	}

	public function getLatestOrderNumber(): int
	{
		$latestOrder = $this->createQueryBuilder('p')
			->select('p.orderNumber')
			->orderBy('p.orderNumber', 'DESC')
			->setMaxResults(1)
			->getQuery()
			->getOneOrNullResult();

		return $latestOrder ? intval($latestOrder['orderNumber']) : 1000;
	}

//    /**
//     * @return Purchase[] Returns an array of Purchase objects
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

//    public function findOneBySomeField($value): ?Purchase
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
	public function getPurchases(?string $status)
	{
		$query = $this->createQueryBuilder('row');

		//Add row.total
		//Add row.customer


		if ($status and in_array($status, array_values(Purchase::STATES))){
			$query->andWhere('row.status = :status')
				->setParameter('status', array_search($status, Purchase::STATES));
		} else if ($status and $status === "confirmed"){
			$query->andWhere('row.confirmed = 1');
		} else if ($status and $status === "not_confirmed"){
			$query->andWhere('row.status = 0');
		}

		return $query;
	}
}
