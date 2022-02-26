<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\True_;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Captcha\Bundle\CaptchaBundle\Validator\Constraints as CaptchaAssert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 *     fields={"email"},
 *     message="le email existe déja"
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Get creative and think of a nom!")
     * @Assert\Length(
     *     min=3,
     *     max=50,
     *     minMessage="The name must be at least 3 characters long",
     *     maxMessage="The name cannot be longer than 50 characters"
     * )
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Get creative and think of a prenom!")
     * @Assert\Length(
     *     min=3,
     *     max=50,
     *     minMessage="The name must be at least 3 characters long",
     *     maxMessage="The name cannot be longer than 50 characters"
     * )
     */
    private $prenom;
    /**
     * @Assert\NotBlank(message="Get creative and think of a password!")
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *     min=8,
     *     max=15,
     *     minMessage="The pass must be at least 8 characters long",
     *     maxMessage="The name cannot be longer than 15 characters"
     * )
     */
    private $password;

    /**
     * @ORM\Column(type="string")
     */
    private $roles;
    /**
     * @ORM\Column(type="integer")
     */
    private $status= 1;
    /**
     * @ORM\Column(type="datetime", nullable=false, options={"default" : "CURRENT_TIMESTAMP"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Write your email please!")
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     */
    private $email;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $imageFile ;

    /**
     * @Assert\EqualTo(propertyPath="password",message="vous n'avez pas tapé le meme message ")
     */
    public $confirmPass ;

    /**
     * @ORM\OneToOne(targetEntity=Panier::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $panier;

    /**
     * @ORM\OneToMany(targetEntity=Panier::class, mappedBy="user")
     */
    private $paniers;

    /**
     * @ORM\ManyToMany(targetEntity=Salle::class, inversedBy="users")
     */
    private $IdSalle;
    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $activation_token;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $reset_token;


    protected $captchaCode;

    /**
     * @ORM\OneToMany(targetEntity=Produit::class, mappedBy="user", orphanRemoval=true)
     */
    private $Products;

    /**
     * @ORM\OneToMany(targetEntity=Categories::class, mappedBy="user", orphanRemoval=true)
     */
    private $CategorieProduit;

    public function getCaptchaCode()
    {
        return $this->captchaCode;
    }

    public function setCaptchaCode($captchaCode)
    {
        $this->captchaCode = $captchaCode;
    }
    public function __construct()
    {
        $this->paniers = new ArrayCollection();
        $this->IdSalle = new ArrayCollection();
        $this->Products = new ArrayCollection();
        $this->CategorieProduit = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function setNom(string $nom)
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom()
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }
    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;

        return $this;
    }
    public function setRoles($roles){
        $this->roles = $roles ;
        return $this;
    }


    public function getRoles()
    {
        return [$this->roles];

    }
    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus(int $status)
    {
        $this->status = $status;

        return $this;
    }
    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;

        return $this;
    }
    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function setImageFile(string $imageFile)
    {
        $this->imageFile = $imageFile;

        return $this;
    }
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $created_at)
    {
        $this->createdAt = $created_at;

        return $this;
    }


    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUsername()
    {
        // TODO: Implement getUsername() method.
    }

    public function getPanier(): ?Panier
    {
        return $this->panier;
    }

    public function setPanier(Panier $panier): self
    {
        // set the owning side of the relation if necessary
        if ($panier->getUser() !== $this) {
            $panier->setUser($this);
        }

        $this->panier = $panier;

        return $this;
    }
    public function __toString()
    {
        return (string) $this->getEmail();
    }

    /**
     * @return Collection<int, Panier>
     */
    public function getPaniers(): Collection
    {
        return $this->paniers;
    }

    public function addPanier(Panier $panier): self
    {
        if (!$this->paniers->contains($panier)) {
            $this->paniers[] = $panier;
            $panier->setUser($this);
        }

        return $this;
    }

    public function removePanier(Panier $panier): self
    {
        if ($this->paniers->removeElement($panier)) {
            // set the owning side to null (unless already changed)
            if ($panier->getUser() === $this) {
                $panier->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Salle>
     */
    public function getIdSalle(): Collection
    {
        return $this->IdSalle;
    }

    public function addIdSalle(Salle $idSalle): self
    {
        if (!$this->IdSalle->contains($idSalle)) {
            $this->IdSalle[] = $idSalle;
        }

        return $this;
    }

    public function removeIdSalle(Salle $idSalle): self
    {
        $this->IdSalle->removeElement($idSalle);

        return $this;
    }
    public function getActivationToken(): ?string
    {
        return $this->activation_token;
    }

    public function setActivationToken(?string $activation_token): self
    {
        $this->activation_token = $activation_token;

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->reset_token;
    }

    public function setResetToken(?string $reset_token): self
    {
        $this->reset_token = $reset_token;

        return $this;
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getProducts(): Collection
    {
        return $this->Products;
    }

    public function addProduct(Produit $product): self
    {
        if (!$this->Products->contains($product)) {
            $this->Products[] = $product;
            $product->setUser($this);
        }

        return $this;
    }

    public function removeProduct(Produit $product): self
    {
        if ($this->Products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getUser() === $this) {
                $product->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Categories>
     */
    public function getCategorieProduit(): Collection
    {
        return $this->CategorieProduit;
    }

    public function addCategorieProduit(Categories $categorieProduit): self
    {
        if (!$this->CategorieProduit->contains($categorieProduit)) {
            $this->CategorieProduit[] = $categorieProduit;
            $categorieProduit->setUser($this);
        }

        return $this;
    }

    public function removeCategorieProduit(Categories $categorieProduit): self
    {
        if ($this->CategorieProduit->removeElement($categorieProduit)) {
            // set the owning side to null (unless already changed)
            if ($categorieProduit->getUser() === $this) {
                $categorieProduit->setUser(null);
            }
        }

        return $this;
    }
}