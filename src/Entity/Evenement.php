<?php

namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups ;

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
     * @Groups("post:read")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Le nom doit etre non vide")
     * @Assert\Length(
     *      max = 50,
     *      maxMessage=" Très long !"
     *
     *     )
     * @ORM\Column(type="string", length=255)
     * @Groups("post:read")

     */
    private $NomEvent;


    /**
     * @ORM\Column(type="date")
     * @Groups("post:read")
     * @Assert\GreaterThanOrEqual("today", message="La date  est incorrecte .")
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
     * @Groups("post:read")
     */
    private $DescriptionEvent;

    /**
     * @Assert\NotBlank(message="Le lieu ne doit pas etre vide")
     * @ORM\Column(type="string", length=1000)
     * @Groups("post:read")
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
     * @Groups("post:read")
     *
     */
    private $NbrParticipantsEvent;

    /**
     * @ORM\ManyToOne(targetEntity=CategoriesEvent::class, inversedBy="evenementss")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("post:read")
     *
     */
    private $NomCategorie;

    /**
     * @ORM\OneToMany(targetEntity=Participation::class, mappedBy="idEvent", orphanRemoval=true)
     * @Groups("post:read")
     */
    private $participations;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("post:read")
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("post:read")
     */
    private $Etat;

    public function __construct()
    {
        $this->participations = new ArrayCollection();
    }


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

    /**
     * @return Collection<int, Participation>
     */
    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function addParticipation(Participation $participation): self
    {
        if ($this->participations->contains($participation)) {
            return false ;
        }
        $participation->setIdEvent($this);
        $this->participations[] = $participation;


        return $this;
    }

    public function removeParticipation(Participation $participation): self
    {
        if ($this->participations->removeElement($participation)) {
            // set the owning side to null (unless already changed)
            if ($participation->getIdEvent() === $this) {
                $participation->setIdEvent(null);
            }
        }

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->Etat;
    }

    public function setEtat(?string $Etat): self
    {
        $this->Etat = $Etat;

        return $this;
    }



}