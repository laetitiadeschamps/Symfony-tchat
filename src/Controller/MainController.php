<?php

namespace App\Controller;

use App\Repository\ChatRepository;
use App\Repository\FriendshipRepository;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class MainController extends AbstractController
{
    private $security;
    private $friendshipRepository;
    private $messageRepository;
    private $chatRepository;

    public function __construct(Security $security, FriendshipRepository $friendshipRepository, MessageRepository $messageRepository, ChatRepository $chatRepository)
    {
        $this->security = $security;
        $this->friendshipRepository = $friendshipRepository;
        $this->messageRepository = $messageRepository;
        $this->chatRepository = $chatRepository;
    }
    /**
     * @Route("/", name="main-home", methods={"GET"})
     * @param void
     * @return Response
     */
    public function home(): Response
    {
       /** @var User */
       $user = $this->security->getUser();
       $requests = $this->friendshipRepository->getFriendshipRequests($user);    
       $notifications = $this->messageRepository->findUnreadCount($user);
       $count = 0;
       // notification has one entry per chat, we thus iterate over chats and increment accordingly the total count of unread messages, and we also build a new array that has one entry per chat and holds chat infos + number of unread messages
       /** @var Array $chatNotifications */
       $chatNotifications=[];
       if($notifications) {
           foreach($notifications as $notification) {
                $count+= $notification['sumMessages'];
                $chatNotifications[$notification['chatId']]['sum']= $notification['sumMessages'];
                $chatNotifications[$notification['chatId']]['chat']=$this->chatRepository->find($notification['chatId']);
           }
       } 
       
       return $this->render('main/home.html.twig', [
           'requests'=>$requests,
           'notifications'=>$chatNotifications,
           'count'=>$count

       ]);
    }
}
