<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\SousDossierRepository")
 */
class SousDossier
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Dossier", inversedBy="subFolders")
     * @ORM\JoinColumn(name="mainfolder_id", referencedColumnName="id")
     */
    private $mainFolder;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ReportCatalog", mappedBy="subFolder")
     */
    private $rapport;

    public function __construct()
    {
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

    public function getMainFolder(): ?Dossier
    {
        return $this->mainFolder;
    }

    public function setMainFolder(?Dossier $mainFolder): self
    {
        $this->mainFolder = $mainFolder;

        return $this;
    }

    public function __toString()
    {
        return $this->getNomDossier();
        // TODO: Implement __toString() method.
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
            $rapport->setSubFolder($this);
        }

        return $this;
    }

    public function removeRapport(ReportCatalog $rapport): self
    {
        if ($this->rapport->contains($rapport)) {
            $this->rapport->removeElement($rapport);
            // set the owning side to null (unless already changed)
            if ($rapport->getSubFolder() === $this) {
                $rapport->setSubFolder(null);
            }
        }

        return $this;
    }
}
