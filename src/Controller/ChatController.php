<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}
