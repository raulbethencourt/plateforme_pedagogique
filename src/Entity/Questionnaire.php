<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Serializable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\QuestionnaireRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuestionnaireRepository", repositoryClass=QuestionnaireRepository::class)
 * @Vich\Uploadable
 */
class Questionnaire implements Serializable
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

    /**
     * @Assert\File(
     *  mimeTypes = {"image/jpeg", "image/png", "image/svg+xml"},
     *  mimeTypesMessage = "Chargé un image correct - jpeg, png -"    
     * )
     * @Vich\UploadableField(mapping="questionnaire_image", fileNameProperty="imageName")
     *
     * @var File|null
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    private $imageName;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTimeInterface|null
     */
    private $updatedAt;

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

    /**
     * Method to check if a questionnaire has at list 1 question
     * and the question has at list 2 propositions
     * @return bool
     */
    public function isPlayable(): bool
    {
        if(count($this->questions) === 0) {
            return false;
        }
        return $this->questions->forAll(function($key, $question) {
            return count($question->getPropositions()) >= 2;
        });
    }

    /**
     * @param  File|UploadedFile|null  $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        // Only change the updated af if the file is really uploaded to avoid database updates.
        // This is needed when the file should be set when loading the entity.
        if ($this->imageFile instanceof UploadedFile) {
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->imageName,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->imageName,
            ) = unserialize($serialized, array('allowed_classes' => false));
    }
}
