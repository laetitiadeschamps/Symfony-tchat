<?php

namespace App\Repository;

use App\Entity\Chat;
use App\Entity\User;
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

    /** Method to get all chats for one user 
    * @param User
    * @return Chat[] 
    */
    public function getNotifications(User $user) {
        $chats = $user->getChats();
        $chatIds=[];
        foreach($chats as $chat) {
            $chatIds[]=$chat->getId();
        }
        $qb = $this->createQueryBuilder('chat')->leftJoin('chat.messages', 'messages')->join('chat.users', 'users')->addSelect('users', 'messages')->where('chat.id IN (:chats)')->setParameter(':chats', (implode(",", $chatIds)))->orderBy('chat.updatedAt');
        $query = $qb->getQuery();
        return $query->getResult();
    }
    /**
     * method to get the chat of 2 users
     *
     * @param integer $userId
     * @param integer $contactId
     * @return Chat
     */
    public function getChatIdFromUsers(int $userId, int $contactId): ?Chat
    {

        return $this->createQueryBuilder('chat')
            ->join('chat.users', 'users')
            ->where(':userId MEMBER OF chat.users')
            ->andWhere(':contactId MEMBER OF chat.users')
            ->setParameter(':userId', $userId)
            ->setParameter(':contactId', $contactId)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
