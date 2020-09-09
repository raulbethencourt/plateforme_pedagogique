<?php

namespace App\Entity;

use App\Repository\PasserRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PasserRepository::class)
 */
class Passer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $points;

    /**
     * @ORM\Column(type="date")
     */
    private $date_realisation;

    /**
     * @ORM\ManyToOne(targetEntity=Etudiant::class, inversedBy="passers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $etudiant;

    /**
     * @ORM\ManyToOne(targetEntity=Questionnaire::class, inversedBy="passers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $questionnaire;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): self
    {
        $this->points = $points;

        return $this;
    }

    public function getDateRealisation(): ?\DateTimeInterface
    {
        return $this->date_realisation;
    }

    public function setDateRealisation(\DateTimeInterface $date_realisation): self
    {
        $this->date_realisation = $date_realisation;

        return $this;
    }

    public function getEtudiant(): ?Etudiant
    {
        return $this->etudiant;
    }

    public function setEtudiant(?Etudiant $etudiant): self
    {
        $this->etudiant = $etudiant;

        return $this;
    }

    public function getQuestionnaire(): ?Questionnaire
    {
        return $this->questionnaire;
    }

    public function setQuestionnaire(?Questionnaire $questionnaire): self
    {
        $this->questionnaire = $questionnaire;

        return $this;
    }
}
