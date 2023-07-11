<?php

namespace App\Entity;

use App\Repository\StudentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StudentRepository::class)
 */
class Student extends User
{
    /**
     * @ORM\OneToMany(targetEntity=Pass::class, mappedBy="student")
     */
    private $pass;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $hobby;

    public function __construct()
    {
        parent::__construct();

        $this->pass = new ArrayCollection();
    }

    public function getPass(): Collection
    {
        return $this->pass;
    }

    public function addPass(Pass $pass): self
    {
        if (!$this->pass->contains($pass)) {
            $this->pass[] = $pass;
            $pass->setstudent($this);
        }

        return $this;
    }

    public function removePass(Pass $pass): self
    {
        if ($this->pass->contains($pass)) {
            $this->pass->removeElement($pass);
            // set the owning side to null (unless already changed)
            if ($pass->getStudent() === $this) {
                $pass->setStudent(null);
            }
        }

        return $this;
    }

    public function getHobby()
    {
        return $this->hobby;
    }

    public function setHobby($hobby): void
    {
        $this->hobby = $hobby;
    }
}
