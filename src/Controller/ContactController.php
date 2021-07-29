<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\RememberMe\ResponseListener;

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
      /**
     * @Route("/search/{string}", name="search", requirements={"string"="\w+"})
     */
    public function search($string, UserRepository $userRepository): Response
    {
        $users = $userRepository->findWithString($string);
        $response = new JsonResponse($users);
        return $response;

    }
}
