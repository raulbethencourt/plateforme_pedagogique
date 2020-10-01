<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Invite
{
    /**
     * @var string|null
     * @Assert\NotBlank
     * @Assert\Email
     */
    private $email;

    /**
     * @var string|null
     * @Assert\NotBlank
     */
    private $type;

    /**
     * @var string|null
     * @Assert\NotBlank
     */
    private $name;

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
}