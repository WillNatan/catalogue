<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DossierRepository")
 */
class Dossier
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomDossier;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SousDossier", mappedBy="mainFolder", cascade={"persist"})
     */
    private $subFolders;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ReportCatalog", mappedBy="mainFolder", cascade={"persist"})
     */
    private $rapport;

    public function __construct()
    {
        $this->sousDossiers = new ArrayCollection();
        $this->subFolders = new ArrayCollection();
        $this->rapport = new ArrayCollection();
    }

    public function getId(): ?int
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
     * @return Collection|ReportCatalog[]
     */
    public function getRapport(): Collection
    {
        return $this->rapport;
    }

    public function addRapport(ReportCatalog $rapport): self
    {
        if (!$this->rapport->contains($rapport)) {
            $this->rapport[] = $rapport;
            $rapport->setMainFolder($this);
        }

        return $this;
    }

    public function removeRapport(ReportCatalog $rapport): self
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
}
