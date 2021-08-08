<?php
namespace App\Controller;
use App\Entity\Chat;
use App\Entity\Friendship;
use App\Entity\User;
use App\Repository\ChatRepository;
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
    private $security;
    private $userRepository;
    private $em;
    private $chatRepository;

    public function __construct(Security $security, UserRepository $userRepository, EntityManagerInterface $em, FriendshipRepository $friendshipRepository, ChatRepository $chatRepository)
    {
        $this->security = $security;
        $this->userRepository = $userRepository;
        $this->em = $em;
        $this->friendshipRepository = $friendshipRepository;
        $this->chatRepository = $chatRepository;
    }
     /**
     * @Route("/list", name="list", methods={"GET"})
     * @param void
     * @return Response
     */
    public function list(): Response
    {
        // We retrieve the current user and get his friends
        /** @var User */
        $user = $this->security->getUser();
        /** @var Friendship */
        $contacts = $user->getFriendsWithMe();
        //We create two arrays to hold friends and friendship requests awaiting approval from either user
        $friends = [];
        $contactsPendingApproval = [];
        // for every friend, we check relationship status, and insert it in the matching array
        foreach($contacts as $contact) {
            
            if($contact->getStatus()==0) {
                if($contact->getRequester() !== $user) {
                    $contactsPendingApproval[]=$contact;
                }
                
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
     * @Route("/profile/{id}", name="profile", methods={"GET"})
     * @param integer
     * @return Response
     */
    public function profile(int $id): Response
    {
        /** @var User */
        $contact=$this->userRepository->find($id);
        return $this->render('contact/profile.html.twig', [
            'contact' => $contact
        ]);
    }
    /**
     * Route called upon requesting friendship with a user
     * @Route("/requestBefriend/{id}", name="requestBefriend")
     * @param integer
     * @return Response
     */
    public function requestBefriend(int $id): Response
    {
        /** @var User $contact */
        $contact=$this->userRepository->find($id);
        /** @var User $user */
        $user=$this->security->getUser();

        // we create two new Friendship entries with status 0, while it hasn't been accepted by the other party
        $friendship = new Friendship();
        $friendship->setUser($contact);
        $friendship->setFriend($user);
        $friendship->setRequester($user);
        $this->em->persist($friendship);

        $friendship2 = new Friendship();
        $friendship2->setUser($user);
        $friendship2->setFriend($contact);
        $friendship2->setRequester($user);
        $this->em->persist($friendship2);

        $this->em->flush();
        $this->addFlash(
            'info',
            'La demande d\'ajout a bien été envoyée!'
        );
        return $this->redirectToRoute('contact-profile', ['id'=> $contact->getId()]);
    }
    /**
     * Route called when someone approves a friendship request, it creates a chat between both users if there is none
     * @Route("/befriend/{id}", name="befriend")
     * @param integer
     * @return Response
     */
    public function befriend(int $id): Response
    {
        /** @var User $contact */
        $contact=$this->userRepository->find($id);
        /** @var User  */
        $user=$this->security->getUser();

        // We set the status of the Friendship entries for both users to 1.
        $friendship = $this->friendshipRepository->findBy(['user'=>$user, 'friend'=>$contact]);
        $friendship[0]->setStatus(1);
        $friendship = $this->friendshipRepository->findBy(['user'=>$contact, 'friend'=>$user]);
        $friendship[0]->setStatus(1);
        
        // if no chat exists between both users, create one
        $chatId = $this->chatRepository->getChatIdFromUsers($user->getId(), $contact->getId());
         if(!$chatId) {
            $chat = new Chat();
            $user->addChat($chat);
            $contact->addChat($chat);
            $this->em->persist($chat);
        }
         
         $this->em->flush();  
         $this->addFlash(
            'info',
            'La liste des contacts a été mise à jour'
        );
        return $this->redirectToRoute('main-home');
    }
    /**
     * Route called when someone rejects a friend request or unfriends someone
     * @Route("/unfriend/{id}", name="unfriend")
     * @param integer
     * @return Response
     */
    public function unfriend(int $id, Request $request): Response
    {
        /** @var User $contact  */
        $contact=$this->userRepository->find($id);
        /** @var User $user  */
        $user=$this->security->getUser();
        $friendship = $this->friendshipRepository->findBy(['user'=>$user, 'friend'=>$contact]);
        $this->em->remove($friendship[0]);
        $friendship = $this->friendshipRepository->findBy(['user'=>$contact, 'friend'=>$user]);
        $this->em->remove($friendship[0]);
        $this->em->flush();  
        $this->addFlash(
            'info',
            $contact->getLogin().' ne fait plus partie de vos amis'
        );
        return new RedirectResponse($request->headers->get('referer'));
    }
    /**
     * Route called by AJAX to search from the user database with a string input
     * @Route("/search/{string}", name="search", requirements={"string"="\w+"})
     * @param string
     * @return Response
     */
    public function search(string $string): Response
    {
        $users = $this->userRepository->findWithString($string);
        $response = new JsonResponse($users);
        return $response;
    }

     /**
     * Route called by AJAX to search all users from the database
     * @Route("/search", name="search")
     * @param void
     * @return Response
     */
    public function findAll(): Response
    {
        $users = $this->userRepository->findAll();
      
        return $this->json($users, 200, [], [
            'groups'=>'contacts'
        ]);
    }
}
