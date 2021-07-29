<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
* @Route("/contact", name="contact-", requirements={"id":"\d+"})
*/
class ContactController extends AbstractController
{
     /**
     * @Route("/list", name="list")
     */
    public function list(Request $request, Security $security, UserRepository $userRepository): Response
    {
        /** @var User */
        $user = $security->getUser();

        /** @var Friendship */
        $contacts = $user->getFriendsWithMe();
        $friends = [];
        $contactsPendingApproval = [];

        foreach($contacts as $contact) {
          if($contact->getStatus()==0) {
                $contactsPendingApproval[]=$contact;
          }else {
                $friends[]=$contact;
          }
        }
       
       
        return $this->render('contact/list.html.twig', [
            'friends'=>$friends,
            'contactsPendingApproval'=> $contactsPendingApproval
        ]);
    }
}
