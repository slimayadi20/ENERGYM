<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups ;

/**
 * @ORM\Entity(repositoryClass=ReclamationRepository::class)
 */
class Reclamation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("post:read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Get creative and think of a title!")
     * @Assert\Length(
     *     min=3,
     *     max=50,
     *     minMessage="The title must be at least 3 characters long",
     *     maxMessage="The title cannot be longer than 50 characters"
     * )
     * @Groups("post:read")
     */
    private $titre;

    /**
     * @ORM\Column(type="datetime")
     */
    private $DateCreation;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Get creative and think of a content!")
     * @Assert\Length(
     *     min=3,
     *     max=1000,
     *     minMessage="The content must be at least 3 characters long",
     *     maxMessage="The content cannot be longer than 150 characters"
     * )
     * @Groups("post:read")
     */
    private $contenu;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="reclamations")
     */
    private $NomUser;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $statut;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $produit;
    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->DateCreation;
    }

    public function setDateCreation(\DateTimeInterface $DateCreation): self
    {
        $this->DateCreation = $DateCreation;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getNomUser(): ?User
    {
        return $this->NomUser;
    }

    public function setNomUser(?User $NomUser): self
    {
        $this->NomUser = $NomUser;

        return $this;
    }

    public function __toString()
    {
        return (string) $this->NomUser;
    }

    public function getProduit(): ?string
    {
        return $this->produit;
    }

    public function setProduit(string $produit): self
    {
        $this->produit = $produit;

        return $this;
    }
}
