<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MatriceRepository")
 */
class Matrice
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid", unique=true)
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Dossier", inversedBy="matrices" ,cascade={"remove"})
     */
    private $domaine;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Liens", mappedBy="matrice" ,cascade={"remove", "persist"})
     */
    private $liens;

    public function __construct()
    {
        $this->liens = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDomaine(): ?Dossier
    {
        return $this->domaine;
    }

    public function setDomaine(?Dossier $domaine): self
    {
        $this->domaine = $domaine;

        return $this;
    }

    /**
     * @return Collection|Liens[]
     */
    public function getLiens(): Collection
    {
        return $this->liens;
    }

    public function addLien(Liens $lien): self
    {
        if (!$this->liens->contains($lien)) {
            $this->liens[] = $lien;
            $lien->setMatrice($this);
        }

        return $this;
    }

    public function removeLien(Liens $lien): self
    {
        if ($this->liens->contains($lien)) {
            $this->liens->removeElement($lien);
            // set the owning side to null (unless already changed)
            if ($lien->getMatrice() === $this) {
                $lien->setMatrice(null);
            }
        }

        return $this;
    }
    public function __toString()
    {
        return (string) $this->getId();
    }
}
