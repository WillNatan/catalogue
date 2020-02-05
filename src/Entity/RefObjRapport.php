<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RefObjRapportRepository")
 */
class RefObjRapport
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid", unique=true)
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ReportCatalog", inversedBy="refObjRapports")
     */
    private $rapport;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ReferentielObjets", inversedBy="refObjRapports")
     */
    private $objet;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomRapport;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomObjet;

    public function getId()
    {
        return $this->id;
    }

    public function getRapport(): ?ReportCatalog
    {
        return $this->rapport;
    }

    public function setRapport(?ReportCatalog $rapport): self
    {
        $this->rapport = $rapport;

        return $this;
    }

    public function getObjet(): ?ReferentielObjets
    {
        return $this->objet;
    }

    public function setObjet(?ReferentielObjets $objet): self
    {
        $this->objet = $objet;

        return $this;
    }

    public function getNomRapport(): ?string
    {
        return $this->nomRapport;
    }

    public function setNomRapport(string $nomRapport): self
    {
        $this->nomRapport = $nomRapport;

        return $this;
    }

    public function getNomObjet(): ?string
    {
        return $this->nomObjet;
    }

    public function setNomObjet(string $nomObjet): self
    {
        $this->nomObjet = $nomObjet;

        return $this;
    }

    public function __toString()
    {
        return (string) $this->getId();
    }
}
