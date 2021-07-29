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
    
        
     
       
        
        return $this->render('main/home.html.twig', [
            'requests'=>$requests
        ]);
    }
}
