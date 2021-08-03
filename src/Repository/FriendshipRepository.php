<?php

namespace App\Repository;

use App\Entity\Friendship;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Friendship|null find($id, $lockMode = null, $lockVersion = null)
 * @method Friendship|null findOneBy(array $criteria, array $orderBy = null)
 * @method Friendship[]    findAll()
 * @method Friendship[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FriendshipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Friendship::class);
    }

    /** Method to get all friendship requests (frindships with status 0) for one user 
    * @param User
    * @return Frienship[]
    */
    public function getFriendshipRequests(User $user) {
        $id=$user->getId();
        $qb = $this->createQueryBuilder('friendship')->join('friendship.user', 'user')->join('friendship.friend', 'friend')->addSelect('user', 'friend')
        ->where('friendship.user = :user')->andWhere('friendship.status = 0')->andWhere('friendship.requester != :user')->setParameter(':user', "$id");
        $query = $qb->getQuery();
        return $query->getResult();
    }
   
   
}
