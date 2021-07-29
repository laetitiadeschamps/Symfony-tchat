<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Entity\Friendship;
use App\Entity\User;
use App\Repository\FriendshipRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
     * @Route("/profile/{id}", name="profile")
     */
    public function profile(int $id, UserRepository $userRepository, Security $security): Response
    {
        $contact=$userRepository->find($id);

        return $this->render('contact/profile.html.twig', [
            'contact' => $contact
        ]);
    }
    /**
     * @Route("/requestBefriend/{id}", name="requestBefriend")
     */
    public function requestBefriend(int $id, UserRepository $userRepository, Security $security, EntityManagerInterface $entityManager): Response
    {
         $contact=$userRepository->find($id);
        /** @var User $user */
         $user=$security->getUser();
        $friendship = new Friendship();
        $friendship->setUser($contact);
        $friendship->setFriend($user);
        $friendship->setRequester($user);
        $entityManager->persist($friendship);

        $friendship2 = new Friendship();
        $friendship2->setUser($user);
        $friendship2->setFriend($contact);
        $friendship2->setRequester($user);
        $entityManager->persist($friendship2);

        $entityManager->flush();
        $this->addFlash(
            'info',
            'La demande d\'ajout a bien été envoyée!'
        );
        return $this->redirectToRoute('contact-profile', ['id'=> $contact->getId()]);
    }
    /**
     * @Route("/befriend/{id}", name="befriend", methods={"GET"})
     */
    public function befriend(int $id, UserRepository $userRepository, Security $security, EntityManagerInterface $em, FriendshipRepository $friendshipRepository): Response
    {
         $contact=$userRepository->find($id);
        /** @var User  */
         $user=$security->getUser();

         $friendship = $friendshipRepository->findBy(['user'=>$user, 'friend'=>$contact]);
         $friendship[0]->setStatus(1);
         $friendship = $friendshipRepository->findBy(['user'=>$contact, 'friend'=>$user]);
         $friendship[0]->setStatus(1);
         $chat = new Chat();
         $user->addChat($chat);
         $contact->addChat($chat);
         $em->persist($chat);
         $em->flush();  
         $this->addFlash(
            'info',
            'La liste des contacts a été mise à jour'
        );
        return $this->redirectToRoute('main-home');
    }
    /**
     * @Route("/unfriend/{id}", name="unfriend", methods={"GET"})
     */
    public function unfriend(int $id, Request $request, UserRepository $userRepository, Security $security, EntityManagerInterface $em, FriendshipRepository $friendshipRepository): Response
    {
         $contact=$userRepository->find($id);
        /** @var User $user  */
         $user=$security->getUser();
         $friendship = $friendshipRepository->findBy(['user'=>$user, 'friend'=>$contact]);
         $em->remove($friendship[0]);
         $friendship = $friendshipRepository->findBy(['user'=>$contact, 'friend'=>$user]);
         $em->remove($friendship[0]);
         $em->flush();  
         $this->addFlash(
            'info',
            $contact->getLogin().' ne fait plus partie de vos amis'
        );
        return new RedirectResponse($request->headers->get('referer'));
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
