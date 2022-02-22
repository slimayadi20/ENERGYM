<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PanierRepository::class)
 */
class Panier
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(type="array")
     */
    private $userPanier = [];

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="paniers")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }



    public function getUserPanier(): ?array
    {
        return $this->userPanier;
    }

    public function setUserPanier(array $userPanier): self
    {
        $this->userPanier = $userPanier;

        return $this;
    }
    public function __toString()
    {
        return (string) $this->getUser();
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
}
