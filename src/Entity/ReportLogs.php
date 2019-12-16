<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\ReportLogsRepository")
 */
class ReportLogs
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
    private $nomRapport;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $numero;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ver;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $com;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $category;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $obj;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $details;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sources;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $parametres;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $histVer;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mfolder;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subfolder;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $createdby;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastupdate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $updateNb;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $updatedBy;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $creationDate;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(?string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getVer(): ?string
    {
        return $this->ver;
    }

    public function setVer(?string $ver): self
    {
        $this->ver = $ver;

        return $this;
    }

    public function getCom(): ?string
    {
        return $this->com;
    }

    public function setCom(?string $com): self
    {
        $this->com = $com;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getObj(): ?string
    {
        return $this->obj;
    }

    public function setObj(?string $obj): self
    {
        $this->obj = $obj;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): self
    {
        $this->details = $details;

        return $this;
    }

    public function getSources(): ?string
    {
        return $this->sources;
    }

    public function setSources(?string $sources): self
    {
        $this->sources = $sources;

        return $this;
    }

    public function getParametres(): ?string
    {
        return $this->parametres;
    }

    public function setParametres(?string $parametres): self
    {
        $this->parametres = $parametres;

        return $this;
    }

    public function getHistVer(): ?string
    {
        return $this->histVer;
    }

    public function setHistVer(?string $histVer): self
    {
        $this->histVer = $histVer;

        return $this;
    }

    public function getMfolder(): ?string
    {
        return $this->mfolder;
    }

    public function setMfolder(?string $mfolder): self
    {
        $this->mfolder = $mfolder;

        return $this;
    }

    public function getSubfolder(): ?string
    {
        return $this->subfolder;
    }

    public function setSubfolder(?string $subfolder): self
    {
        $this->subfolder = $subfolder;

        return $this;
    }

    public function getCreatedby(): ?string
    {
        return $this->createdby;
    }

    public function setCreatedby(?string $createdby): self
    {
        $this->createdby = $createdby;

        return $this;
    }

    public function getLastupdate(): ?\DateTimeInterface
    {
        return $this->lastupdate;
    }

    public function setLastupdate(?\DateTimeInterface $lastupdate): self
    {
        $this->lastupdate = $lastupdate;

        return $this;
    }

    public function getUpdateNb(): ?int
    {
        return $this->updateNb;
    }

    public function setUpdateNb(?int $updateNb): self
    {
        $this->updateNb = $updateNb;

        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?string $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(?\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }
}
