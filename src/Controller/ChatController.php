<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Repository\ChatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
* @Route("/chat", name="chat-", requirements={"id":"\d+"})
*/
class ChatController extends AbstractController
{
     /**
     * @Route("/list", name="list")
     */
    public function list(): Response
    {
        return $this->render('chat/list.html.twig');
    }
    /**
     * @Route("/getChat/{id}", name="getChat")
     */
    public function getChat(Chat $chat, Security $security, ChatRepository $chatRepository, EntityManagerInterface $em): Response
    {
        // Enable access to chat if logged in user is part of the chat participants
        $this->denyAccessUnlessGranted('view', $chat);
        // When accessing a tchat, we mark as read all messages from the chat where we are not the author
        foreach($chat->getMessages() as $message) {
            if($message->getAuthor() != $security->getUser()) {
                $message->setIsRead(true);
            }
        }
        $em->flush();
        return $this->render('chat/getChat.html.twig', [
            'chat'=>$chat
        ]);
        
    }
}
