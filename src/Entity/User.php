<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\DateTime as ConstraintsDateTime;
/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email", "login"}, message="L'email et le login doivent être uniques")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"contacts"})
     */
    private $id;

    /**
     * @Assert\Email(
     *     message = "Le mail '{{ value }}' n'est pas valide."
     * )
     * @Assert\NotBlank(message="L'email ne peut pas être vide")
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"contacts"})
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @Assert\NotBlank(message="Le login ne peut pas être vide")
     * @ORM\Column(type="string", length=255)
     * @Groups({"contacts"})
     */
    private $login;

    /**
     * @Assert\NotBlank(message="Le prénom ne peut pas être vide")
     * @ORM\Column(type="string", length=255)
     * @Groups({"contacts"})
     */
    private $firstname;

    /**
     * @Assert\NotBlank(message="Le nom ne peut pas être vide")
     * @ORM\Column(type="string", length=255)
     * @Groups({"contacts"})
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"contacts"})
     */
    private $picture;

    /**
     * @Assert\NotBlank(message="La date de naissance ne peut pas être vide")
     * @Assert\LessThan(value="today", message="Date non valide")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $birthdate;

    /**
     * @ORM\Column(type="integer", options={"default": "1"})
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="author", orphanRemoval=true)
     */
    private $messages;

     /**
     * The people who I think are my friends.
     *
     * @ORM\OneToMany(targetEntity="Friendship", mappedBy="user")
     */
    private $friends;

    /**
     * The people who think that I’m their friend.
     *
     * @ORM\OneToMany(targetEntity="Friendship", mappedBy="friend")
     */
    private $friendsWithMe;

    /**
     * @ORM\ManyToMany(targetEntity=Chat::class, mappedBy="users")
     */
    private $chats;

    /**
     * @ORM\OneToMany(targetEntity=Friendship::class, mappedBy="requester")
     */
    private $friendships;
    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
        $this->messages = new ArrayCollection();
        $this->friendsWithMe = new ArrayCollection();
        $this->friends = new ArrayCollection();
        $this->chats = new ArrayCollection();
        $this->friendships = new ArrayCollection();
        $this->status = 1;

    }
    public function addFriendship(Friendship $friendship)
    {
        $this->friends->add($friendship);
        $friendship->friend->addFriendshipWithMe($friendship);
    }

    public function addFriendshipWithMe(Friendship $friendship)
    {
        $this->friendsWithMe->add($friendship);
    }

    public function addFriend(User $friend)
    {
        $fs = new Friendship();
        $fs->setUser($this);
        $fs->setFriend($friend);
        // set defaults
        $fs->setStatus(0);

        $this->addFriendship($fs);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture ?? 'avatar-male-1.png';
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(?\DateTimeInterface $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setAuthor($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getAuthor() === $this) {
                $message->setAuthor(null);
            }
        }

        return $this;
    }

    

    /**
     * @return Collection|Chat[]
     */
    public function getChats(): Collection
    {
        return $this->chats;
    }

    public function addChat(Chat $chat): self
    {
        if (!$this->chats->contains($chat)) {
            $this->chats[] = $chat;
            $chat->addUser($this);
        }

        return $this;
    }

    public function removeChat(Chat $chat): self
    {
        if ($this->chats->removeElement($chat)) {
            $chat->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection|Friendship[]
     */
    public function getFriendships(): Collection
    {
        return $this->friendships;
    }

    public function removeFriendship(Friendship $friendship): self
    {
        if ($this->friendships->removeElement($friendship)) {
            // set the owning side to null (unless already changed)
            if ($friendship->getRequester() === $this) {
                $friendship->setRequester(null);
            }
        }

        return $this;
    }

    /**
     * Get the people who I think are my friends.
     */ 
    public function getFriends()
    {
        return $this->friends;
    }

    /**
     * Set the people who I think are my friends.
     *
     * @return  self
     */ 
    public function setFriends($friends)
    {
        $this->friends = $friends;

        return $this;
    }

    /**
     * Get the people who think that I’m their friend.
     */ 
    public function getFriendsWithMe()
    {
        return $this->friendsWithMe;
    }

    /**
     * Set the people who think that I’m their friend.
     *
     * @return  self
     */ 
    public function setFriendsWithMe($friendsWithMe)
    {
        $this->friendsWithMe = $friendsWithMe;

        return $this;
    }
}
