<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReferentielObjetsRepository")
 */
class ReferentielObjets
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid", unique=true)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nomObjet;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $schemaObj;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tableobj;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $champ;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $qualification;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RefObjRapport", mappedBy="objet")
     */
    private $refObjRapports;


    public function __construct()
    {
        $this->refObjRapports = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
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

    public function getSchemaObj(): ?string
    {
        return $this->schemaObj;
    }

    public function setSchemaObj(string $schemaObj): self
    {
        $this->schemaObj = $schemaObj;

        return $this;
    }

    public function getChamp(): ?string
    {
        return $this->champ;
    }

    public function setChamp(string $champ): self
    {
        $this->champ = $champ;

        return $this;
    }

    /**
     * @return Collection|RefObjRapport[]
     */
    public function getRefObjRapports(): Collection
    {
        return $this->refObjRapports;
    }

    public function addRefObjRapport(RefObjRapport $refObjRapport): self
    {
        if (!$this->refObjRapports->contains($refObjRapport)) {
            $this->refObjRapports[] = $refObjRapport;
            $refObjRapport->setObjet($this);
        }

        return $this;
    }

    public function removeRefObjRapport(RefObjRapport $refObjRapport): self
    {
        if ($this->refObjRapports->contains($refObjRapport)) {
            $this->refObjRapports->removeElement($refObjRapport);
            // set the owning side to null (unless already changed)
            if ($refObjRapport->getObjet() === $this) {
                $refObjRapport->setObjet(null);
            }
        }

        return $this;
    }

    public function getTableobj(): ?string
    {
        return $this->tableobj;
    }

    public function setTableobj(?string $tableobj): self
    {
        $this->tableobj = $tableobj;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getQualification(): ?string
    {
        return $this->qualification;
    }

    public function setQualification(?string $qualification): self
    {
        $this->qualification = $qualification;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function __toString()
    {
        return (string) $this->getId();
    }
}
