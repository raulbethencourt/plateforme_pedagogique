<?php

namespace App\Entity;

use App\Repository\QuestionnaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=QuestionnaireRepository::class)
 */
class Questionnaire
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
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $difficulte;

    /**
     * @ORM\OneToMany(targetEntity=Question::class, mappedBy="questionnaire", orphanRemoval=true, cascade={"persist"})
     */
    private $questions;

    /**
     * @ORM\ManyToOne(targetEntity=Formateur::class, inversedBy="questionnaires")
     * @ORM\JoinColumn(nullable=false)
     */
    private $formateur;

    /**
     * @ORM\OneToMany(targetEntity=Passer::class, mappedBy="questionnaire", orphanRemoval=true)
     */
    private $passers;

    public function __construct()
    {
        $this->questionnaire = new ArrayCollection();
        $this->questions = new ArrayCollection();
        $this->passers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDifficulte(): ?string
    {
        return $this->difficulte;
    }

    public function setDifficulte(string $difficulte): self
    {
        $this->difficulte = $difficulte;

        return $this;
    }

    /**
     * @return Collection|Question[]
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->setQuestionnaire($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->contains($question)) {
            $this->questions->removeElement($question);
            // set the owning side to null (unless already changed)
            if ($question->getQuestionnaire() === $this) {
                $question->setQuestionnaire(null);
            }
        }

        return $this;
    }

    public function getFormateur(): ?Formateur
    {
        return $this->formateur;
    }

    public function setFormateur(?Formateur $formateur): self
    {
        $this->formateur = $formateur;

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
            $passer->setQuestionnaire($this);
        }

        return $this;
    }

    public function removePasser(Passer $passer): self
    {
        if ($this->passers->contains($passer)) {
            $this->passers->removeElement($passer);
            // set the owning side to null (unless already changed)
            if ($passer->getQuestionnaire() === $this) {
                $passer->setQuestionnaire(null);
            }
        }

        return $this;
    }
}
