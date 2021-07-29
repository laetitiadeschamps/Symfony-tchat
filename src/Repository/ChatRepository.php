<?php

namespace App\Repository;

use App\Entity\Chat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Chat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chat[]    findAll()
 * @method Chat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chat::class);
    }
    public function getActiveChats($user) {
      $chats = $user->getChats();
      $chatIds=[];
      foreach($chats as $chat) {
          $chatIds[]=$chat->getId();
      }
      $chatIds = implode(', ', $chatIds);
        $qb = $this->createQueryBuilder('chat')->leftJoin('chat.messages', 'messages')->join('chat.users', 'users')->addSelect('users', 'messages')->where('chat.id IN ('. $chatIds.')')->orderBy('chat.updatedAt');
        

        $query = $qb->getQuery();
      
     
        return $query->getResult();
    }

    public function getNotifications($user) {
        $chats = $user->getChats();
        $chatIds=[];
        foreach($chats as $chat) {
            $chatIds[]=$chat->getId();
        }
          $qb = $this->createQueryBuilder('chat')->leftJoin('chat.messages', 'messages')->join('chat.users', 'users')->addSelect('users', 'messages')->where('chat.id IN (:chats)')->andWhere('chat.')->setParameter(':chats', (implode(",", $chatIds)))->orderBy('chat.updatedAt');
          
  
          $query = $qb->getQuery();
        
          dd($query->getResult());
          return $query->getResult();
      }
      public function findUnreadCount($user) {
        $chats = $user->getChats();
        $chatIds=[];
        foreach($chats as $chat) {
            $chatIds[]=$chat->getId();
        }
        
        $qb = $this->createQueryBuilder('chat')->join('chat.messages', 'messages')->select('SUM(messages) as sumMessages')->where('chat.id IN (:chats)')->andWhere('messages.isRead = 0')->andWhere('messages.author != :user')->setParameter(':chats', (implode(",", $chatIds)))->setParameter(':user', $user->getId())->orderBy('chat.updatedAt');
        $query = $qb->getQuery();
        
          dd($query->getResult());
          return $query->getResult();
    }

    // /**
    //  * @return Chat[] Returns an array of Chat objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Chat
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
