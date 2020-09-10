<?php

namespace App\Entity;

use App\Repository\FormateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FormateurRepository::class)
 */
class Formateur extends User
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
    private $code_formateur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $photo_name;

    /**
     * @ORM\OneToMany(targetEntity=Questionnaire::class, mappedBy="formateur")
     */
    private $questionnaires;

    /**
     * @ORM\OneToMany(targetEntity=Classe::class, mappedBy="formateur")
     */
    private $classes;

    public function __construct()
    {
        $this->questionnaires = new ArrayCollection();
        $this->classes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeFormateur(): ?string
    {
        return $this->code_formateur;
    }

    public function setCodeFormateur(string $code_formateur): self
    {
        $this->code_formateur = $code_formateur;

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
            $questionnaire->setFormateur($this);
        }

        return $this;
    }

    public function removeQuestionnaire(Questionnaire $questionnaire): self
    {
        if ($this->questionnaires->contains($questionnaire)) {
            $this->questionnaires->removeElement($questionnaire);
            // set the owning side to null (unless already changed)
            if ($questionnaire->getFormateur() === $this) {
                $questionnaire->setFormateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Classe[]
     */
    public function getClasses(): Collection
    {
        return $this->classes;
    }

    public function addClass(Classe $class): self
    {
        if (!$this->classes->contains($class)) {
            $this->classes[] = $class;
            $class->setFormateur($this);
        }

        return $this;
    }

    public function removeClass(Classe $class): self
    {
        if ($this->classes->contains($class)) {
            $this->classes->removeElement($class);
            // set the owning side to null (unless already changed)
            if ($class->getFormateur() === $this) {
                $class->setFormateur(null);
            }
        }

        return $this;
    }

    /**
     * @param mixed $id
     * @return Formateur
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
