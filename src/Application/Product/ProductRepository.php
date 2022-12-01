<?php

namespace App\Application\Product;

use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use App\Application\Shop\Data\SearchData;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
	private PaginatorInterface $paginator;

	public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Product::class);
	    $this->paginator = $paginator;
    }

    public function add(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

	/**
	 * @param string[] $ids
	 * @return mixed
	 */
	public function removeByIds(array $ids): mixed
	{
		return $this->createQueryBuilder('p')
			->where('p.id in (:ids)')
			->setParameter('ids', $ids)
			->delete()
			->getQuery()
			->execute();
	}

	public function findSearch(SearchData $search): PaginationInterface
	{
		$query = $this->getSearchQuery($search)->getQuery();
		return $this->paginator->paginate($query, $search->page, 15);
	}

	public function getSearchQuery(SearchData $search, bool $ignorePrice = false): QueryBuilder
	{
		$query = $this->createQueryBuilder('p')
			->select('c', 'p')
			->join('p.categories', 'c');

		if (!empty($search->q) && $ignorePrice === false) {
			$query = $query
				->andWhere('p.name LIKE :q')
				->setParameter('q', "%{$search->q}%");
		}

		if (!empty($search->min) && $ignorePrice === false) {
			$query = $query
				->andWhere('p.fixedPrice >= :min')
				->setParameter('min', $search->min);
		}

		if (!empty($search->max) && $ignorePrice === false) {
			$query = $query
				->andWhere('p.fixedPrice <= :max')
				->setParameter('max', $search->max);
		}

		/*if (!empty($search->promo)) {
			$query = $query
				->andWhere('p.promo = 1');
		}*/

		if (!empty($search->categories) && $ignorePrice === false) {
			$query = $query
				->andWhere('c.id IN (:categories)')
				->setParameter('categories', $search->categories);
		}

		return $query->orderBy('p.created_at', 'ASC');
	}

	/**
	 * @param SearchData $search
	 * @return int[]
	 */
	public function findMinMax(SearchData $search): array
	{
		$result = $this->getSearchQuery($search, true)
			->select('MIN(p.fixedPrice) as min', 'Max(p.fixedPrice) as max')
			->getQuery()
			->getScalarResult();
		return [intval($result[0]['min'], $result[0]['max'])];
	}



//    /**
//     * @return Product[] Returns an array of Product objects
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

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
