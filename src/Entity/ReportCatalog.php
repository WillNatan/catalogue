<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReportCatalogRepository")
 */
class ReportCatalog
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $n = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Nom_Rapport = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $VersionActuelle = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $Commentaire = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Categorie = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $Objectifs = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $Details = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $Sources = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $Parametres = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $Historique_Versions = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SousDossier", inversedBy="rapport")
     */
    private $subFolder = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Dossier", inversedBy="rapport")
     */
    private $mainFolder = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="reports")
     */
    private $user = null;

    /**
     * @ORM\Column(type="datetime")
     */
    private $last_update = null;

    /**
     * @ORM\Column(type="integer")
     */
    private $update_nb = null;

    public function __construct()
    {
        $this->user = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getN(): ?int
    {
        return $this->n;
    }

    public function setN(int $n): self
    {
        $this->n = $n;

        return $this;
    }

    public function getNomRapport(): ?string
    {
        return $this->Nom_Rapport;
    }

    public function setNomRapport(string $Nom_Rapport): self
    {
        $this->Nom_Rapport = $Nom_Rapport;

        return $this;
    }

    public function getVersionActuelle(): ?string
    {
        return $this->VersionActuelle;
    }

    public function setVersionActuelle(string $VersionActuelle): self
    {
        $this->VersionActuelle = $VersionActuelle;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->Commentaire;
    }

    public function setCommentaire(string $Commentaire): self
    {
        $this->Commentaire = $Commentaire;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->Categorie;
    }

    public function setCategorie(string $Categorie): self
    {
        $this->Categorie = $Categorie;

        return $this;
    }

    public function getObjectifs(): ?string
    {
        return $this->Objectifs;
    }

    public function setObjectifs(string $Objectifs): self
    {
        $this->Objectifs = $Objectifs;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->Details;
    }

    public function setDetails(string $Details): self
    {
        $this->Details = $Details;

        return $this;
    }

    public function getParametres(): ?string
    {
        return $this->Parametres;
    }

    public function setParametres(string $Parametres): self
    {
        $this->Parametres = $Parametres;

        return $this;
    }

    public function getHistoriqueVersions(): ?string
    {
        return $this->Historique_Versions;
    }

    public function setHistoriqueVersions(string $Historique_Versions): self
    {
        $this->Historique_Versions = $Historique_Versions;

        return $this;
    }

    public function getSubFolder(): ?SousDossier
    {
        return $this->subFolder;
    }

    public function setSubFolder(?SousDossier $subFolder): self
    {
        $this->subFolder = $subFolder;

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

    public function getSources(): ?string
    {
        return $this->Sources;
    }

    public function setSources(string $Sources): self
    {
        $this->Sources = $Sources;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getLastUpdate(): ?\DateTimeInterface
    {
        return $this->last_update;
    }

    public function setLastUpdate(\DateTimeInterface $last_update): self
    {
        $this->last_update = $last_update;

        return $this;
    }

    public function getUpdateNb(): ?int
    {
        return $this->update_nb;
    }

    public function setUpdateNb(int $update_nb): self
    {
        $this->update_nb = $update_nb;

        return $this;
    }
}
