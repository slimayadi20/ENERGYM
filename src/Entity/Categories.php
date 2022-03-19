<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use App\Repository\CategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CategoriesRepository::class)
 */
class Categories
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("post:read")
     */
    private $id;

    public function getId(): ?int
    {
        return $this->id;
    }
    /**
     * @Assert\NotBlank(message="Le nom doit etre non vide")
     * @Assert\Length(
     *      max = 30,
     *      maxMessage=" Très long !"
     *
     *     )
     * @ORM\Column(type="string", length=255)
     *
     * @Groups("post:read")
     */
    private $nom;

    /**
     * @ORM\OneToMany(targetEntity=Produit::class, mappedBy="nomCateg")
     */
    private $nomProduit;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="CategorieProduit")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity=Produit::class, mappedBy="categories")
     */
    private $produits;

    public function __construct()
    {

        $this->nomProduit = new ArrayCollection();
        $this->produits = new ArrayCollection();
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





    /**
     * @return Collection|Produit[]
     */
    public function getNomProduit(): Collection
    {
        return $this->nomProduit;
    }

    public function addNomProduit(Produit $nomProduit): self
    {
        if (!$this->nomProduit->contains($nomProduit)) {
            $this->nomProduit[] = $nomProduit;
            $nomProduit->setNomCateg($this);
        }

        return $this;
    }

    public function removeNomProduit(Produit $nomProduit): self
    {
        if ($this->nomProduit->removeElement($nomProduit)) {
            // set the owning side to null (unless already changed)
            if ($nomProduit->getNomCateg() === $this) {
                $nomProduit->setNomCateg(null);
            }
        }

        return $this;
    }
    public function __toString()
    {
        return (string) $this->getNom();
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function addProduit(Produit $produit): self
    {
        if (!$this->produits->contains($produit)) {
            $this->produits[] = $produit;
            $produit->addCategory($this);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): self
    {
        if ($this->produits->removeElement($produit)) {
            $produit->removeCategory($this);
        }

        return $this;
    }
}