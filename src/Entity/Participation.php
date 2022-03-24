<?php

namespace App\Entity;

use App\Repository\ParticipationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ParticipationRepository::class)
 * @UniqueEntity("idUser")
 */
class Participation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("post:read")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="participations")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("post:read")

     */
    private $idUser;

    /**
     * @ORM\ManyToOne(targetEntity=Evenement::class, inversedBy="participations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idEvent;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("post:read")
     */
    private $VerificationCode;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(?User $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getIdEvent(): ?Evenement
    {
        return $this->idEvent;
    }

    public function setIdEvent(?Evenement $idEvent): self
    {
        $this->idEvent = $idEvent;

        return $this;
    }
    public function __toString()
    {
        return (string) $this->getIdUser();
    }

    public function getVerificationCode(): ?int
    {
        return $this->VerificationCode;
    }

    public function setVerificationCode(?int $VerificationCode): self
    {
        $this->VerificationCode = $VerificationCode;

        return $this;
    }

}
