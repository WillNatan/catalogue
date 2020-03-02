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
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid", unique=true)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomDossier;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Domaines", inversedBy="subFolders")
     * @ORM\JoinColumn(name="mainfolder_id", referencedColumnName="id")
     */
    private $mainFolder;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Reports", mappedBy="subFolder")
     */
    private $rapport;

    public function __construct()
    {
        $this->rapport = new ArrayCollection();
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

    public function getMainFolder(): ?Domaines
    {
        return $this->mainFolder;
    }

    public function setMainFolder(?Domaines $mainFolder): self
    {
        $this->mainFolder = $mainFolder;

        return $this;
    }

    public function __toString()
    {
        return (string) $this->getId();
        // TODO: Implement __toString() method.
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
            $rapport->setSubFolder($this);
        }

        return $this;
    }

    public function removeRapport(Reports $rapport): self
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
