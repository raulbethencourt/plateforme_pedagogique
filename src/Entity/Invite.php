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
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param  string|null  $type
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    /**
     * @var string|null
     * @Assert\NotBlank
     */
    private $name;


    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param  string|null  $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param  string|null  $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

}