<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use App\Repository\SalleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SalleRepository::class)
 */
class Salle
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *@Assert\NotBlank

     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=500)
     * @Assert\NotBlank
     */
    private $adresse;

    /**
     * @ORM\Column(type="bigint")
     * @Assert\NotBlank
     * @Assert\Positive(message="le numero doit etre positif")
     * @Assert\Length(
     *      min = 8,
     *      max = 8,
     *      minMessage = "numero de telephone non valide ! ",
     *      maxMessage = "numero de telephone non valide !" )
     */

    private $tel;

    /**
     * @ORM\Column(type="string", length=250)
     * @Assert\Email(
     *     message = "cette adresse ( '{{ value }}' ) n'est pas valide."
     * )
     */

    private $mail;

    /**
     * @ORM\Column(type="string", length=500)
     * @Assert\NotBlank
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Assert\Positive(message="le prix doit etre positif")
     */
    private $prix1;
    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Assert\Positive(message="le prix doit etre positif")
     */
    private $prix2;
    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Assert\Positive(message="le prix doit etre positif")
     */
    private $prix3;

    /**
     * @ORM\Column(type="time")

     */
    private $heureo;

    /**
     * @ORM\Column(type="time")
     */
    private $heuref;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\OneToMany(targetEntity=Cours::class, mappedBy="salleassocie", orphanRemoval=true)
     */
    private $cours;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="IdSalle")
     */
    private $users;


    public function __construct()
    {
        $this->cours = new ArrayCollection();
        $this->users = new ArrayCollection();
    }




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


    public function __toString()
    {
        return (string) $this->getNom();
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

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

    public function getPrix1(): ?int
    {
        return $this->prix1;
    }

    public function setPrix1(int $prix1): self
    {
        $this->prix1 = $prix1;

        return $this;
    }
    public function getPrix2(): ?int
    {
        return $this->prix2;
    }

    public function setPrix2(int $prix2): self
    {
        $this->prix2 = $prix2;

        return $this;
    }
    public function getPrix3(): ?int
    {
        return $this->prix3;
    }

    public function setPrix3(int $prix3): self
    {
        $this->prix3 = $prix3;

        return $this;
    }

    public function getHeureo(): ?\DateTimeInterface
    {
        return $this->heureo;
    }

    public function setHeureo(\DateTimeInterface $heureo): self
    {
        $this->heureo = $heureo;

        return $this;
    }

    public function getHeuref(): ?\DateTimeInterface
    {
        return $this->heuref;
    }

    public function setHeuref(\DateTimeInterface $heuref): self
    {
        $this->heuref = $heuref;

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

    /**
     * @return Collection|Cours[]
     */
    public function getCours(): Collection
    {
        return $this->cours;
    }

    public function addCour(Cours $cour): self
    {
        if (!$this->cours->contains($cour)) {
            $this->cours[] = $cour;
            $cour->setSalleassocie($this);
        }

        return $this;
    }

    public function removeCour(Cours $cour): self
    {
        if ($this->cours->removeElement($cour)) {
            // set the owning side to null (unless already changed)
            if ($cour->getSalleassocie() === $this) {
                $cour->setSalleassocie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addIdSalle($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeIdSalle($this);
        }

        return $this;
    }

}

