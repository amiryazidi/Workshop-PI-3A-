<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\StudentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
class Student
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"nsc is requierd")]
    private ?int $nsc = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"email is requierd")]
    #[Assert\Email(message:"the email is not valid mail")]
    private ?string $email = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNsc(): ?int
    {
        return $this->nsc;
    }

    public function setNsc(int $nsc): self
    {
        $this->nsc = $nsc;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
}
