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

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Liens", mappedBy="Indicateur")
     */
    private $liens;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Liens", mappedBy="Axes")
     */
    private $liensAxes;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $denomination;


    public function __construct()
    {
        $this->refObjRapports = new ArrayCollection();
        $this->liens = new ArrayCollection();
        $this->liensAxes = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNomObjet()
    {
        return $this->nomObjet;
    }

    public function setNomObjet($nomObjet): self
    {
        $this->nomObjet = $nomObjet;

        return $this;
    }

    public function getSchemaObj()
    {
        return $this->schemaObj;
    }

    public function setSchemaObj($schemaObj): self
    {
        $this->schemaObj = $schemaObj;

        return $this;
    }

    public function getChamp()
    {
        return $this->champ;
    }

    public function setChamp($champ): self
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

    public function getTableobj()
    {
        return $this->tableobj;
    }

    public function setTableobj($tableobj): self
    {
        $this->tableobj = $tableobj;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getQualification()
    {
        return $this->qualification;
    }

    public function setQualification($qualification): self
    {
        $this->qualification = $qualification;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description): self
    {
        $this->description = $description;

        return $this;
    }

    public function __toString()
    {
        return (string) $this->getId();
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
            $lien->setIndicateur($this);
        }

        return $this;
    }

    public function removeLien(Liens $lien): self
    {
        if ($this->liens->contains($lien)) {
            $this->liens->removeElement($lien);
            // set the owning side to null (unless already changed)
            if ($lien->getIndicateur() === $this) {
                $lien->setIndicateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Liens[]
     */
    public function getLiensAxes(): Collection
    {
        return $this->liensAxes;
    }

    public function addLiensAxe(Liens $liensAxe): self
    {
        if (!$this->liensAxes->contains($liensAxe)) {
            $this->liensAxes[] = $liensAxe;
            $liensAxe->addAxe($this);
        }

        return $this;
    }

    public function removeLiensAxe(Liens $liensAxe): self
    {
        if ($this->liensAxes->contains($liensAxe)) {
            $this->liensAxes->removeElement($liensAxe);
            $liensAxe->removeAxe($this);
        }

        return $this;
    }

    public function getDenomination()
    {
        return $this->denomination;
    }

    public function setDenomination($denomination): self
    {
        $this->denomination = $denomination;

        return $this;
    }
}
