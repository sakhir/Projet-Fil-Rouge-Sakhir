<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ApiResource(
 * attributes={
 *      "pagination_enabled"=true,
 *      "security" = "is_granted('ROLE_ADMIN')",
 *      "security_message" = "vous n'avez pas accès a cette resource"
 *   },
 * collectionOperations={
 *  "get_users"={
 *          "method"= "GET",
 *          "path" = "/admin/users",
 *          "normalization_context"={"groups"={"user:read"}}
 *   },
 * "create_users"={
 *          "method"= "POST",
 *          "path" = "/admin/users",
 *          "route_name"="create_user",
 *   },
 * },
 * itemOperations={
 *      "get_one_user"={
 *             "method"="GET",
 *             "path" = "/admin/users/{id}",
 *              "normalization_context"={"groups"={"user:read"}},
 *      },
 *      "edit_user"={
 *             "method"="PUT",
 *             "path" = "/admin/users/{id}",
 *      },
 * },
 * 
 * 
 * )

* @ORM\InheritanceType("SINGLE_TABLE")
* @ORM\DiscriminatorColumn(name = "type", type = "string")
* @ORM\DiscriminatorMap({"formateur"="Formateur","CM"= "CM", "apprenant"="Apprenant", "admin"="User"})
*/
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"profil:read","user:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"profil:read","user:read"})
      
     */
    private $email;

    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="integer")
     */
    private $isdeleted;

    /**
     * @ORM\Column(type="blob", nullable=true)
     */
    private $avatar;

    /**
     * @ORM\Column(type="integer")
     */
    private $isconnect;

    /**
     * @ORM\ManyToOne(targetEntity=Profil::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $profil;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_'.$this->profil->getLibelle();

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
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

    public function getIsdeleted(): ?int
    {
        return $this->isdeleted;
    }

    public function setIsdeleted(int $isdeleted): self
    {
        $this->isdeleted = $isdeleted;

        return $this;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function setAvatar($avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getIsconnect(): ?int
    {
        return $this->isconnect;
    }

    public function setIsconnect(int $isconnect): self
    {
        $this->isconnect = $isconnect;

        return $this;
    }

    public function getProfil(): ?Profil
    {
        return $this->profil;
    }

    public function setProfil(?Profil $profil): self
    {
        $this->profil = $profil;

        return $this;
    }
}
