<?php

namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

use App\Repository\CategoriesEventRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoriesEventRepository::class)
 */
class CategoriesEvent
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
    private $nomCategorie;

    /**
     * @ORM\OneToMany(targetEntity=Evenement::class, mappedBy="NomCategorie", orphanRemoval=true)
     */
    private $evenementss;

    public function __construct()
    {
        $this->evenementss = new ArrayCollection();
    }




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomCategorie(): ?string
    {
        return $this->nomCategorie;
    }

    public function setNomCategorie(string $nomCategorie): self
    {
        $this->nomCategorie = $nomCategorie;

        return $this;
    }

    /**
     * @return Collection|Evenement[]
     */
    public function getEvenementss(): Collection
    {
        return $this->evenementss;
    }

    public function addEvenementss(Evenement $evenementss): self
    {
        if (!$this->evenementss->contains($evenementss)) {
            $this->evenementss[] = $evenementss;
            $evenementss->setNomCategorie($this);
        }

        return $this;
    }

    public function removeEvenementss(Evenement $evenementss): self
    {
        if ($this->evenementss->removeElement($evenementss)) {
            // set the owning side to null (unless already changed)
            if ($evenementss->getNomCategorie() === $this) {
                $evenementss->setNomCategorie(null);
            }
        }

        return $this;
    }
    public function __toString()
    {
        return (string) $this->getnomCategorie();
    }




}
