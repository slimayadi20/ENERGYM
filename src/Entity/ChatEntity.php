<?php

namespace App\Entity;

use App\Repository\ChatRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChatRepository::class)
 */
class ChatEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="chatSender", cascade={"persist", "remove"})
     */
    private $IdSender;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="chatReceiver", cascade={"persist", "remove"})
     */
    private $IdReceiver;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $message;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdSender(): ?User
    {
        return $this->IdSender;
    }

    public function setIdSender(?User $IdSender): self
    {
        $this->IdSender = $IdSender;

        return $this;
    }

    public function getIdReceiver(): ?User
    {
        return $this->IdReceiver;
    }

    public function setIdReceiver(?User $IdReceiver): self
    {
        $this->IdReceiver = $IdReceiver;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }


}
