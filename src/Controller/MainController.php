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
    /**
     * @Route("/", name="main-home")
     */
    public function home(Security $security, FriendshipRepository $friendshipRepository, MessageRepository $messageRepository, ChatRepository $chatRepository): Response
    {
       /** @var User */
       $user = $security->getUser();
       $requests = $friendshipRepository->getFriendshipRequests($user);
       
       $notifications = $messageRepository->findUnreadCount($user);
       $count = 0;
       $chatNotifications=[];
       if($notifications) {
           foreach($notifications as $notification) {
               $count+= $notification['sumMessages'];
              $chatNotifications[$notification['chatId']]['sum']= $notification['sumMessages'];
              $chatNotifications[$notification['chatId']]['chat']=$chatRepository->find($notification['chatId']);
           }
       } 
       
       return $this->render('main/home.html.twig', [
           'requests'=>$requests,
           'notifications'=>$chatNotifications,
           'count'=>$count

       ]);
    }
}
