<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }
    public function findUnreadCount($user) {
        $chats = $user->getChats();
        $chatIds=[];
        foreach($chats as $chat) {
            $chatIds[]=$chat->getId();
        }
        $chatIds = implode(', ', $chatIds);
       
        if($chatIds) {
            $qb = $this->createQueryBuilder('message')->join('message.chat', 'chat')->select('(chat) as chatId', 'SUM(CASE WHEN message.isRead = 0 AND message.author != :user THEN 1 ELSE 0 END) as sumMessages')->where('message.chat IN ('.$chatIds.')')->having('sumMessages > 0')->setParameter(':user', $user->getId())->groupBy('message.chat');
            $query = $qb->getQuery();
            
              
            return $query->getResult();
        } else  {
            return null;
        }
        
       
    }


    // /**
    //  * @return Message[] Returns an array of Message objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Message
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
