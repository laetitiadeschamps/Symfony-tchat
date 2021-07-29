<?php

namespace App\Entity;

use App\Repository\TypeRepository;
use DateTime as GlobalDateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;


/**
 * @ORM\Entity(repositoryClass=FriendshipRepository::class)
 */
class Friendship
{
    /**
     * @ORM\ManyToOne(targetEntity="User",inversedBy="friends")
     * @ORM\JoinColumn(nullable=false)
     * @ORM\Id
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="User",inversedBy="friendsWithMe")
     * @ORM\JoinColumn(nullable=false)
     * @ORM\Id
     */
    private $friend;

    /**
     *
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $requestedAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="friendships")
     * @ORM\JoinColumn(nullable=false)
     */
    private $requester;

    public function __construct()
    {
        $this->requestedAt = new GlobalDateTime();
        $this->status = 0;
    }

    /**
     * Get example of an additional attribute.
     */ 
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set example of an additional attribute.
     *
     * @return  self
     */ 
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the value of friend
     */ 
    public function getFriend()
    {
        return $this->friend;
    }

    /**
     * Set the value of friend
     *
     * @return  self
     */ 
    public function setFriend($friend)
    {
        $this->friend = $friend;

        return $this;
    }

    /**
     * Get the value of user
     */ 
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set the value of user
     *
     * @return  self
     */ 
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    public function getRequestedAt()
    {
        return $this->requestedAt;
    }

    public function setRequestedAt(\DateTimeInterface $requestedAt): self
    {
        $this->requestedAt = $requestedAt;

        return $this;
    }

    public function getRequester(): ?User
    {
        return $this->requester;
    }

    public function setRequester(?User $requester): self
    {
        $this->requester = $requester;

        return $this;
    }
}

