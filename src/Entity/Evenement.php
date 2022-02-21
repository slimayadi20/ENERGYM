<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use App\Repository\EvenementRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EvenementRepository::class)
 */
class Evenement
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Le nom doit etre non vide")
     * @Assert\Type(type="alpha", message="Le nom ne doit pas contenir des chiffres .")
     * @Assert\Length(
     *      max = 15,
     *      maxMessage=" Très long !"
     *
     *     )
     * @ORM\Column(type="string", length=255)
     */
    private $NomEvent;


    /**
     * @ORM\Column(type="date")
     * @Assert\GreaterThanOrEqual("today", message="La date  doit etre valide .")
     */
    private $DateEvent;

    /**
     * @Assert\NotBlank(message="Ecrivez quelques chose !")
     * @Assert\Length(
     *      min = 100,
     *      max = 1000,
     *      minMessage = "Description très courte ! ",
     *      maxMessage = "doit etre <=100" )
     * @ORM\Column(type="string", length=1000)
     */
    private $DescriptionEvent;

    /**
     * @Assert\NotBlank(message="Le lieu ne doit pas etre vide")
     * @ORM\Column(type="string", length=1000)
     */
    private $LieuEvent;

    /**
     * @Assert\NotBlank(message="Entrez quelques choses !")
     * @Assert\Positive(message="Le nombre de participants doit etre positif.")
     * @Assert\Type(type="numeric", message="Le nombre ne doit pas contenir des caractères .")
     * @Assert\Range(
     *      min = 10,
     *      max = 1000,
     *      notInRangeMessage = "Nombre très petit",
     *     )
     * @ORM\Column(type="string", length=255)
     */
    private $NbrParticipantsEvent;

    /**
     * @ORM\ManyToOne(targetEntity=CategoriesEvent::class, inversedBy="evenementss")
     * @ORM\JoinColumn(nullable=false)
     */
    private $NomCategorie;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomEvent(): ?string
    {
        return $this->NomEvent;
    }


    public function setNomEvent(string $NomEvent): self
    {
        $this->NomEvent = $NomEvent;

        return $this;
    }
    public function getDateEvent(): ?\DateTimeInterface
    {
        return $this->DateEvent;
    }

    public function setDateEvent(\DateTimeInterface $date): self
    {
        $this->DateEvent = $date;

        return $this;
    }


    public function getDescriptionEvent(): ?string
    {
        return $this->DescriptionEvent;
    }

    public function setDescriptionEvent(string $DescriptionEvent): self
    {
        $this->DescriptionEvent = $DescriptionEvent;

        return $this;
    }

    public function getLieuEvent(): ?string
    {
        return $this->LieuEvent;
    }

    public function setLieuEvent(string $LieuEvent): self
    {
        $this->LieuEvent = $LieuEvent;

        return $this;
    }

    public function getNbrParticipantsEvent(): ?string
    {
        return $this->NbrParticipantsEvent;
    }

    public function setNbrParticipantsEvent(string $NbrParticipantsEvent): self
    {
        $this->NbrParticipantsEvent = $NbrParticipantsEvent;

        return $this;
    }

    public function getNomCategorie(): ?CategoriesEvent
    {
        return $this->NomCategorie;
    }

    public function setNomCategorie(?CategoriesEvent $NomCategorie): self
    {
        $this->NomCategorie = $NomCategorie;

        return $this;
    }
    public function __toString()
    {
        return (string) $this->getNomCategorie();
    }



}
