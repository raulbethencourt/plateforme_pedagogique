<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=QuestionRepository::class)
 */
class Question
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
    private $enonce;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     */
    private $score;

    /**
     * @ORM\OneToMany(targetEntity=Proposition::class, mappedBy="question")
     */
    private $propositions;

    /**
     * @ORM\ManyToOne(targetEntity=Questionnaire::class, inversedBy="questions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $questionnaire;

    public function __construct()
    {
        $this->propositions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEnonce(): ?string
    {
        return $this->enonce;
    }

    public function setEnonce(string $enonce): self
    {
        $this->enonce = $enonce;

        return $this;
    }

    public function getScore(): ?string
    {
        return $this->score;
    }

    public function setScore(string $score): self
    {
        $this->score = $score;

        return $this;
    }

    /**
     * @return Collection|Proposition[]
     */
    public function getpropositions(): Collection
    {
        return $this->propositions;
    }

    public function addPropistion(Proposition $propistion): self
    {
        if (!$this->propositions->contains($propistion)) {
            $this->propositions[] = $propistion;
            $propistion->setQuestion($this);
        }

        return $this;
    }

    public function removePropistion(Proposition $propistion): self
    {
        if ($this->propositions->contains($propistion)) {
            $this->propositions->removeElement($propistion);
            // set the owning side to null (unless already changed)
            if ($propistion->getQuestion() === $this) {
                $propistion->setQuestion(null);
            }
        }

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
