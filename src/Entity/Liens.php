<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LiensRepository")
 */
class Liens
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid", unique=true)
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ReferentielObjets", inversedBy="liens" ,cascade={"persist"})
     */
    private $Indicateur;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ReferentielObjets", inversedBy="liensAxes" ,cascade={"persist"})
     * @ORM\JoinTable(name="LiensAxes_Objets")
     */
    private $Axes;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Matrice", inversedBy="liens" ,cascade={"persist"})
     */
    private $matrice;

    public function __construct()
    {
        $this->Axes = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getIndicateur(): ?ReferentielObjets
    {
        return $this->Indicateur;
    }

    public function setIndicateur(?ReferentielObjets $Indicateur): self
    {
        $this->Indicateur = $Indicateur;

        return $this;
    }

    /**
     * @return Collection|ReferentielObjets[]
     */
    public function getAxes(): Collection
    {
        return $this->Axes;
    }

    public function addAxe(ReferentielObjets $axe): self
    {
        if (!$this->Axes->contains($axe)) {
            $this->Axes[] = $axe;
        }

        return $this;
    }

    public function removeAxe(ReferentielObjets $axe): self
    {
        if ($this->Axes->contains($axe)) {
            $this->Axes->removeElement($axe);
        }

        return $this;
    }

    public function getMatrice(): ?Matrice
    {
        return $this->matrice;
    }

    public function setMatrice(?Matrice $matrice): self
    {
        $this->matrice = $matrice;

        return $this;
    }
    public function __toString()
    {
        return (string) $this->getId();
    }
}
