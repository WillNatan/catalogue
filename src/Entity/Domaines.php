<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\DossierRepository")
 */
class Domaines
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid", unique=true)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomDossier;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SousDossier", mappedBy="mainFolder", cascade={"remove"})
     */
    private $subFolders;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Reports", mappedBy="mainFolder")
     */
    private $rapport;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Matrice", mappedBy="domaine")
     */
    private $matrices;


    public function __construct()
    {
        $this->subFolders = new ArrayCollection();
        $this->rapport = new ArrayCollection();
        $this->matrices = new ArrayCollection();

    }

    public function getId()
    {
        return $this->id;
    }

    public function getNomDossier(): ?string
    {
        return $this->nomDossier;
    }

    public function setNomDossier(string $nomDossier): self
    {
        $this->nomDossier = $nomDossier;

        return $this;
    }

    /**
     * @return Collection|SousDossier[]
     */
    public function getSubFolders(): Collection
    {
        return $this->subFolders;
    }

    public function addSubFolder(SousDossier $subFolder): self
    {
        if (!$this->subFolders->contains($subFolder)) {
            $this->subFolders[] = $subFolder;
            $subFolder->setMainFolder($this);
        }

        return $this;
    }

    public function removeSubFolder(SousDossier $subFolder): self
    {
        if ($this->subFolders->contains($subFolder)) {
            $this->subFolders->removeElement($subFolder);
            // set the owning side to null (unless already changed)
            if ($subFolder->getMainFolder() === $this) {
                $subFolder->setMainFolder(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Reports[]
     */
    public function getRapport(): Collection
    {
        return $this->rapport;
    }

    public function addRapport(Reports $rapport): self
    {
        if (!$this->rapport->contains($rapport)) {
            $this->rapport[] = $rapport;
            $rapport->setMainFolder($this);
        }

        return $this;
    }

    public function removeRapport(Reports $rapport): self
    {
        if ($this->rapport->contains($rapport)) {
            $this->rapport->removeElement($rapport);
            // set the owning side to null (unless already changed)
            if ($rapport->getMainFolder() === $this) {
                $rapport->setMainFolder(null);
            }
        }

        return $this;
    }
    public function __toString()
    {
        return (string) $this->getId();
    }

    /**
     * @return Collection|Matrice[]
     */
    public function getMatrices(): Collection
    {
        return $this->matrices;
    }

    public function addMatrix(Matrice $matrix): self
    {
        if (!$this->matrices->contains($matrix)) {
            $this->matrices[] = $matrix;
            $matrix->setDomaine($this);
        }

        return $this;
    }

    public function removeMatrix(Matrice $matrix): self
    {
        if ($this->matrices->contains($matrix)) {
            $this->matrices->removeElement($matrix);
            // set the owning side to null (unless already changed)
            if ($matrix->getDomaine() === $this) {
                $matrix->setDomaine(null);
            }
        }

        return $this;
    }
}
