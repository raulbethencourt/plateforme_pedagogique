<?php

namespace App\Entity;

use App\Repository\TeacherRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TeacherRepository::class)
 */
class Teacher extends User
{
    /**
     * @ORM\OneToMany(targetEntity=Questionnaire::class, mappedBy="teacher")
     */
    private $questionnaires;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subject;

    /**
     * @ORM\ManyToMany(targetEntity=Classroom::class, mappedBy="teachers")
     */
    private $classrooms;

    public function __construct()
    {
        $this->questionnaires = new ArrayCollection();
        $this->classrooms = new ArrayCollection();
    }

    /**
     * @return Collection|Classroom[]
     */
    public function getClassrooms(): Collection
    {
        return $this->classrooms;
    }

    /**
     * @param Classroom $classroom
     * @return $this
     */
    public function addClassrooms(Classroom $classroom): self
    {
        if (!$this->classrooms->contains($classroom)) {
            $this->classrooms[] = $classroom;
            $classroom->addTeacher($this);
        }

        return $this;
    }

    /**
     * @param Classroom $classroom
     * @return $this
     */
    public function removeClassrooms(Classroom $classroom): self
    {
        if ($this->classrooms->contains($classroom)) {
            $this->classrooms->removeElement($classroom);
            $classroom->removeTeacher($this);
        }

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return Collection|Questionnaire[]
     */
    public function getQuestionnaires(): Collection
    {
        return $this->questionnaires;
    }

    public function addQuestionnaire(Questionnaire $questionnaire): self
    {
        if (!$this->questionnaires->contains($questionnaire)) {
            $this->questionnaires[] = $questionnaire;
            $questionnaire->setTeacher($this);
        }

        return $this;
    }

    public function removeQuestionnaire(Questionnaire $questionnaire): self
    {
        if ($this->questionnaires->contains($questionnaire)) {
            $this->questionnaires->removeElement($questionnaire);
            // set the owning side to null (unless already changed)
            if ($questionnaire->getTeacher() === $this) {
                $questionnaire->setTeacher(null);
            }
        }

        return $this;
    }

    public function addClassroom(Classroom $classroom): self
    {
        if (!$this->classrooms->contains($classroom)) {
            $this->classrooms[] = $classroom;
            $classroom->addTeacher($this);
        }

        return $this;
    }

    public function removeClassroom(Classroom $classroom): self
    {
        if ($this->classrooms->contains($classroom)) {
            $this->classrooms->removeElement($classroom);
            $classroom->removeTeacher($this);
        }

        return $this;
    }
}
