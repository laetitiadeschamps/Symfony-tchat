<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Entity\Message;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
* @Route("/message", name="message-", requirements={"id":"\d+"})
*/
class MessageController extends AbstractController
{
    /**
     * @Route("/add/chat/{id}", name="add")
     */
    public function add(Request $request, Chat $chat, EntityManagerInterface $em, Security $security): Response
    {
        /** @var \App\Entity\User */
        $user=$security->getUser();
        
        $this->denyAccessUnlessGranted('edit', $chat);
        $data = json_decode($request->getContent(), true);
        $messageBody = $data['message'];
        $message = new Message();
        $message->setMessage($messageBody);
        $message->setAuthor($user);
        $message->setChat($chat);
        $chat->setUpdatedAt(new DateTime());
        $em->persist($message);
        $em->flush();


        return $this->json("Message envoyé", 201);
    }
}
