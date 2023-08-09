<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;



class PasswordUpdate
{
    
    #[ORM\Column(length: 255)]
    private ?string $oldPassword = null;

    #[ORM\Column(length: 255)]
    private ?string $newPassword = null;

    #[ORM\Column(length: 255)]
    /**
     * @Assert\EqualTo(propertyPath="newPassword", message="Vou n'avez pas correctement confirme votre nouveau
     * mot de passe")
     */
    private ?string $confirmPassword = null;


    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): self
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): self
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword(string $confirmPassword): self
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }
}
