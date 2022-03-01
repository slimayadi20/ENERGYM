<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups ;
use App\Repository\SalleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SalleRepository::class)
 *
 */
class Salle
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
     *@Assert\NotBlank
     *@Groups("post:read")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=500)
     * @Assert\NotBlank
     * @Groups("post:read")
     */
    private $adresse;

    /**
     * @ORM\Column(type="bigint")
     * @Assert\NotBlank
     * @Assert\Positive(message="le numero doit etre positif")
     * @Groups("post:read")
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
     * @Groups("post:read")
     */

    private $mail;

    /**
     * @ORM\Column(type="string", length=500)
     * @Assert\NotBlank
     * @Groups("post:read")
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Assert\Positive(message="le prix doit etre positif")
     * @Groups("post:read")
     */
    private $prix1;
    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Assert\Positive(message="le prix doit etre positif")
     * @Groups("post:read")
     */
    private $prix2;
    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Assert\Positive(message="le prix doit etre positif")
     * @Groups("post:read")
     */
    private $prix3;

    /**
     * @ORM\Column(type="time")
     * @Groups("post:read")
     */
    private $heureo;

    /**
     * @ORM\Column(type="time")
     * @Groups("post:read")
     */
    private $heuref;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("post:read")
     */
    private $image;

    /**
     * @ORM\OneToMany(targetEntity=Cours::class, mappedBy="salleassocie", orphanRemoval=true)
     */
    private $cours;

    /**
     * @ORM\OneToMany(targetEntity=SalleLike::class, mappedBy="salle", orphanRemoval=true)
     */
    private $likes;



    public function __construct()
    {
        $this->cours = new ArrayCollection();
        $this->likes = new ArrayCollection();
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
     * @return Collection<int, SalleLike>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(SalleLike $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setSalle($this);
        }

        return $this;
    }

    public function removeLike(SalleLike $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getSalle() === $this) {
                $like->setSalle(null);
            }
        }

        return $this;
    }
    /**
     * @param User $user
     * @return boolean
     */
    public function isLikedByUser(user $user) : bool
    {
        foreach($this->likes as $like){
            if ($like->getUser() === $user) return true;
        }
        return false;
    }

}