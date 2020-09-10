<?php

namespace App\Entity;

use App\Repository\EtudiantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EtudiantRepository::class)
 */
class Etudiant extends User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $code_etudiant;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $photo_name;

    /**
     * @ORM\ManyToOne(targetEntity=Classe::class, inversedBy="etudiants")
     * @ORM\JoinColumn(nullable=false)
     */
    private $classe;

    /**
     * @ORM\OneToMany(targetEntity=Passer::class, mappedBy="etudiant")
     */
    private $passers;

    public function __construct()
    {
        $this->passers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeEtudiant(): ?string
    {
        return $this->code_etudiant;
    }

    public function setCodeEtudiant(string $code_etudiant): self
    {
        $this->code_etudiant = $code_etudiant;

        return $this;
    }

    public function getPhotoName(): ?string
    {
        return $this->photo_name;
    }

    public function setPhotoName(string $photo_name): self
    {
        $this->photo_name = $photo_name;

        return $this;
    }

    public function getClasse(): ?Classe
    {
        return $this->classe;
    }

    public function setClasse(?Classe $classe): self
    {
        $this->classe = $classe;

        return $this;
    }

    /**
     * @return Collection|Passer[]
     */
    public function getPassers(): Collection
    {
        return $this->passers;
    }

    public function addPasser(Passer $passer): self
    {
        if (!$this->passers->contains($passer)) {
            $this->passers[] = $passer;
            $passer->setEtudiant($this);
        }

        return $this;
    }

    public function removePasser(Passer $passer): self
    {
        if ($this->passers->contains($passer)) {
            $this->passers->removeElement($passer);
            // set the owning side to null (unless already changed)
            if ($passer->getEtudiant() === $this) {
                $passer->setEtudiant(null);
            }
        }

        return $this;
    }
}
