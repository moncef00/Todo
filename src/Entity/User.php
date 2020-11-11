<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table("user")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("email", message="L'email est déjà utilisé, veuillez en saisir un autre.")
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class User implements UserInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;


    /**
     * @ORM\Column(type="string", length=25, unique=false)
     * @Assert\NotBlank(message="Vous devez saisir un nom d'utilisateur.")
     * @var string
     */
    private $nom;
    /**
 * @ORM\Column(type="string", length=25, unique=false)
 * @Assert\NotBlank(message="Vous devez saisir un nom d'utilisateur.")
 * @var string
 */
    private $prenom;
    /**
 * @ORM\Column(type="string", length=150, unique=false)
 * @Assert\NotBlank(message="Vous devez saisir un nom d'utilisateur.")
 * @var string
 */
    private $societe;
    /**
     * @ORM\Column(type="string", length=25, unique=false)
     * @Assert\NotBlank(message="Vous devez saisir un nom d'utilisateur.")
     * @var string
     */
    private $fonction;

    /**
     * @ORM\Column(type="string", length=64)
     * @var string
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     * @Assert\NotBlank(message="Vous devez saisir une adresse email.")
     * @Assert\Email(message="Le format de l'adresse n'est pas correcte.")
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(type="array")
     * @Assert\NotBlank(message="Vous devez choisir un rôle.")
     * @var array
     */
    private $roles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Task", mappedBy="user", orphanRemoval=true)
     * @var ArrayCollection
     */
    private $tasks;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }
    public function getUsername()
    {
        return $this->email;
    }

    public function getSalt()
    {
        return null;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
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

    public function eraseCredentials(): void
    {
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles[0] = $roles[0];

        return $this;
    }

    /**
     * @return Collection|Task[]
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setUser($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->contains($task)) {
            $this->tasks->removeElement($task);
            // set the owning side to null (unless already changed)
            if ($task->getUser() === $this) {
                $task->setUser(null);
            }
        }

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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getSociete(): ?string
    {
        return $this->societe;
    }

    public function setSociete(string $societe): self
    {
        $this->societe = $societe;

        return $this;
    }

    public function getFonction(): ?string
    {
        return $this->fonction;
    }

    public function setFonction(string $fonction): self
    {
        $this->fonction = $fonction;

        return $this;
    }
}
