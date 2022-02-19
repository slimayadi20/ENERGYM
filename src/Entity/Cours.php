<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;


use App\Repository\CoursRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CoursRepository::class)
 */
class Cours
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank

     */
    private $nom;

    /**
     * @Assert\NotBlank(message="le nom du coach  doit etre non vide")
     * @ORM\Column(type="string", length=255)
     * @Assert\Type(type="alpha", message="Le nom du coach ne doit pas contenir des chiffres .")
     */
    private $nomCoach;


    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Assert\Positive(message="le nombre doit etre positif")
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=500)
     *  @Assert\NotBlank
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $image;


    /**
     * @ORM\Column(type="date")
     * @Assert\GreaterThanOrEqual("today", message="La date  doit etre valide .")
     */

    private $date;

    /**
     * @ORM\Column(type="time")
     */
    private $heureD;

    /**
     * @ORM\Column(type="time")
     */
    private $heureF;

    /**
     * @ORM\ManyToOne(targetEntity=Salle::class, inversedBy="cours")
     * @ORM\JoinColumn(nullable=false)
     *  @Assert\NotBlank(message="il faut choisir une salle de sport")
     */
    private $salleassocie;




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getNomCoach(): ?string
    {
        return $this->nomCoach;
    }

    public function setNomCoach(string $nomCoach): self
    {
        $this->nomCoach = $nomCoach;

        return $this;
    }



    public function getNombre(): ?int
    {
        return $this->nombre;
    }

    public function setNombre(int $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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
    public function __toString()
    {
        return (string) $this->getSalleassocie();
    }



    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getHeureD(): ?\DateTimeInterface
    {
        return $this->heureD;
    }

    public function setHeureD(\DateTimeInterface $heureD): self
    {
        $this->heureD = $heureD;

        return $this;
    }

    public function getHeureF(): ?\DateTimeInterface
    {
        return $this->heureF;
    }

    public function setHeureF(\DateTimeInterface $heureF): self
    {
        $this->heureF = $heureF;

        return $this;
    }

    public function getSalleassocie(): ?Salle
    {
        return $this->salleassocie;
    }

    public function setSalleassocie(?Salle $salleassocie): self
    {
        $this->salleassocie = $salleassocie;

        return $this;
    }













}