<?php

namespace App\Application\User;

use Doctrine\Persistence\ManagerRegistry;
use App\Helper\Repository\ExtendRepositoryTrait;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

	use ExtendRepositoryTrait;

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->add($user, true);
    }

    public function add(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

	/**
	 * Fonction servant à récupérer un utilisateur correspondant à un profil Oauth (discord, google )
	 */
	public function findForOauth(string $service, ?string $serviceID, ?string $email): ?User
	{
		if (null === $serviceID || null === $email) {
			return null;
		}

		return $this->createQueryBuilder('u')
		            ->where('u.email = :email')
		            ->orWhere("u.{$service}Id = :serviceId")
		            ->setMaxResults(1)
		            ->setParameters([
			            'email' => $email,
			            'serviceId' => $serviceID
		            ])
		            ->getQuery()
		            ->getOneOrNullResult();
	}

	/**
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function loadUserByUsername(string $username): ?User
	{
		return $this->loadUserByIdentifier($username);
	}

	/**
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function loadUserByIdentifier(string $identifier): ?User
	{
		return $this->createQueryBuilder('u')
		            ->where('u.username = :val')
		            ->orWhere('u.email = :val')
		            ->setParameter('val', $identifier)
		            ->getQuery()
		            ->getOneOrNullResult();

	}
}
