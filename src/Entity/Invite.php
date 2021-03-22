<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Invite
{
    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Email
     */
    private $email;

    /**
     * @var string
     * @Assert\NotBlank
     */
    private $type;

    /**
     * @var string
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @var string 
     * @Assert\NotBlank
     */
    private $surname;

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

	/**
	 * @return  string
	 */
	public function getSurname(): string
	{
		return $this->surname;
	}

	/**
	 * @param   string  $surname  
	 * @return  self
	 */
	public function setSurname(string $surname): self 
	{
		$this->surname = $surname;

		return $this;
	}
}