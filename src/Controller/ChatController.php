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
    private $em;
    private $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }
     /**
     * @Route("/list", name="list", methods={"GET"})
     * @param void
     * @return Response
     */
    public function list(): Response
    {
        return $this->render('chat/list.html.twig');
    }
    /**
     * Route used to access one chat with its id, a voter decides if access is granted or not
     * @Route("/{id}", name="getChat", methods={"GET"})
     * @param Chat
     * @return Response
     */
    public function getChat(Chat $chat): Response
    {
        // Enable access to chat if logged in user is part of the chat participants
        $this->denyAccessUnlessGranted('view', $chat);
        // When accessing a tchat, we mark as read all messages from the chat where we are not the author
        foreach($chat->getMessages() as $message) {
            if($message->getAuthor() != $this->security->getUser()) {
                $message->setIsRead(true);
            }
        }
        $this->em->flush();
        return $this->render('chat/getChat.html.twig', [
            'chat'=>$chat
        ]);
        
    }
     /**
     * Route used to automatically mark a message as read if the user is connected on the socket, along with all other tchat messages 
     * @Route("/{id}/markAsRead", name="markAsRead", methods={"GET"})
     * @param Chat
     * @return Response
     */
    public function markAsRead(Chat $chat): Response
    {
        $this->denyAccessUnlessGranted('edit', $chat);
        // When we receive a message while we are on the tchat, we mark it as read along with all other tchat messages
        foreach($chat->getMessages() as $message) {
            if($message->getAuthor() != $this->security->getUser()) {
                $message->setIsRead(true);
            }
        }
        $this->em->flush();
        return $this->json('ok', 200);
        
    }
}
