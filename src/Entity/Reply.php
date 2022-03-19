<?php

namespace App\Entity;

use App\Repository\ReplyRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReplyRepository::class)
 */
class Reply
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;



    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Contenu;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $EmailReceiver;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $EmailSender;

    public function getId(): ?int
    {
        return $this->id;
    }



    public function getContenu(): ?string
    {
        return $this->Contenu;
    }

    public function setContenu(?string $Contenu): self
    {
        $this->Contenu = $Contenu;

        return $this;
    }

    public function getEmailReceiver(): ?string
    {
        return $this->EmailReceiver;
    }

    public function setEmailReceiver(?string $EmailReceiver): self
    {
        $this->EmailReceiver = $EmailReceiver;

        return $this;
    }

    public function getEmailSender(): ?string
    {
        return $this->EmailSender;
    }

    public function setEmailSender(?string $EmailSender): self
    {
        $this->EmailSender = $EmailSender;

        return $this;
    }
}
