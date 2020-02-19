<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImportsRepository")
 */
class Imports
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid", unique=true)
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $lastDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $excelFile;

    public function getId()
    {
        return $this->id;
    }

    public function getLastDate(): ?\DateTimeInterface
    {
        return $this->lastDate;
    }

    public function setLastDate(\DateTimeInterface $lastDate): self
    {
        $this->lastDate = $lastDate;

        return $this;
    }

    public function getExcelFile()
    {
        return $this->excelFile;
    }

    public function setExcelFile($excelFile): self
    {
        $this->excelFile = $excelFile;

        return $this;
    }
    public function __toString()
    {
        return (string) $this->getId();
    }
}
