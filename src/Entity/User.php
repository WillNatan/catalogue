<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    
    private $plainPassword;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ReportCatalog", mappedBy="createdBy")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ReportCatalog", mappedBy="updatedBy")
     */
    private $updatedBy;
    
    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $username;

    public function __construct()
    {
        $this->createdBy = new ArrayCollection();
        $this->updatedBy = new ArrayCollection();
    }

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
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

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

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }
    

    /**
     * @return Collection|ReportCatalog[]
     */
    public function getCreatedBy(): Collection
    {
        return $this->createdBy;
    }

    public function addCreatedBy(ReportCatalog $createdBy): self
    {
        if (!$this->createdBy->contains($createdBy)) {
            $this->createdBy[] = $createdBy;
            $createdBy->setCreatedBy($this);
        }

        return $this;
    }

    public function removeCreatedBy(ReportCatalog $createdBy): self
    {
        if ($this->createdBy->contains($createdBy)) {
            $this->createdBy->removeElement($createdBy);
            // set the owning side to null (unless already changed)
            if ($createdBy->getCreatedBy() === $this) {
                $createdBy->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ReportCatalog[]
     */
    public function getUpdatedBy(): Collection
    {
        return $this->updatedBy;
    }

    public function addUpdatedBy(ReportCatalog $updatedBy): self
    {
        if (!$this->updatedBy->contains($updatedBy)) {
            $this->updatedBy[] = $updatedBy;
            $updatedBy->setUpdatedBy($this);
        }

        return $this;
    }

    public function removeUpdatedBy(ReportCatalog $updatedBy): self
    {
        if ($this->updatedBy->contains($updatedBy)) {
            $this->updatedBy->removeElement($updatedBy);
            // set the owning side to null (unless already changed)
            if ($updatedBy->getUpdatedBy() === $this) {
                $updatedBy->setUpdatedBy(null);
            }
        }

        return $this;
    }
}
