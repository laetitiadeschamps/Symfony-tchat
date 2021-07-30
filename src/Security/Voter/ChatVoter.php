<?php

namespace App\Security\Voter;

use App\Entity\Chat;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ChatVoter extends Voter
{
    // these strings are just invented: you can use anything
    const VIEW = 'view';
    const EDIT = 'edit';
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::VIEW, self::EDIT])
            && $subject instanceof \App\Entity\Chat;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
   
        /** @var Chat $chat */
        $chat = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($chat, $user);
            case self::EDIT:
                return $this->canEdit($chat, $user);
        }

        throw new \LogicException('l\'action n\'est pas autorisée!');
    }
    private function canView(Chat $chat, User $user): bool
    {
        // if they can edit, they can view
        if ($this->canEdit($chat, $user)) {
            return true;
        }


        return false;
    }

    private function canEdit(Chat $chat, User $user): bool
    {
       
         if($chat->getUsers()->contains($user) || $this->security->isGranted('ROLE_ADMIN')) {
                return true;
        }
        return false;
        
        
       
    }
}
