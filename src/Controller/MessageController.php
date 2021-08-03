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
    private $security;
    private $em;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->security=$security;
        $this->em = $em;
    }
    /**
     * @Route("/add/chat/{id}", name="add", methods={"GET"})
     * @param Chat
     * @return Response
     */
    public function add(Chat $chat, Request $request): Response
    {
        /** @var \App\Entity\User */
        $user=$this->security->getUser();
        $this->denyAccessUnlessGranted('edit', $chat);
        $data = json_decode($this->request->getContent(), true);
        $messageBody = $data['message'];
        $message = new Message();
        $message->setMessage($messageBody);
        $message->setAuthor($user);
        $message->setChat($chat);
        $chat->setUpdatedAt(new DateTime());
        $this->em->persist($message);
        $this->em->flush();
        return $this->json("Message envoyé", 201);
    }
}
