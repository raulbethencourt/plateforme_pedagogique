<?php

namespace App\Entity;

use App\Repository\QuestionnaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuestionnaireRepository", repositoryClass=QuestionnaireRepository::class)
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
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $difficulty;

    /**
     * @ORM\OneToMany(targetEntity=Question::class, mappedBy="questionnaire",
     *     orphanRemoval=true, cascade={"persist"})
     */
    private $questions;

    /**
     * @ORM\ManyToOne(targetEntity=Teacher::class, inversedBy="questionnaires")
     * @ORM\JoinColumn(nullable=false)
     */
    private $teacher;

    /**
     * @ORM\OneToMany(targetEntity=Pass::class, mappedBy="questionnaire", orphanRemoval=true)
     */
    private $pass;

    /**
     * @ORM\Column(type="date")
     */
    private $date_creation;

    public const DIFFICULTIES = ["facile", "moyen", "difficile"];

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->pass = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDifficulty(): ?string
    {
        return $this->difficulty;
    }

    public function setDifficulty(string $difficulty): self
    {
        $this->difficulty = $difficulty;

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

    public function getTeacher(): ?Teacher
    {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): self
    {
        $this->teacher = $teacher;

        return $this;
    }

    /**
     * @return Collection|Pass[]
     */
    public function getPass(): Collection
    {
        return $this->pass;
    }

    public function addPass(Pass $pass): self
    {
        if (!$this->pass->contains($pass)) {
            $this->pass[] = $pass;
            $pass->setQuestionnaire($this);
        }

        return $this;
    }

    public function removePass(Pass $pass): self
    {
        if ($this->pass->contains($pass)) {
            $this->pass->removeElement($pass);
            // set the owning side to null (unless already changed)
            if ($pass->getQuestionnaire() === $this) {
                $pass->setQuestionnaire(null);
            }
        }

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getTotalScore(): int
    {
        $total = 0;
        foreach($this->questions as $question){
            $total+= $question->getScore();
        }
        return $total;
    }

    public function isPlayable(): bool
    {
        if(count($this->questions) === 0) {
            return false;
        }
        return $this->questions->forAll(function($key, $question) {
            return count($question->getPropositions()) >= 2;
        });
    }
}
