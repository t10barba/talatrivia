<?php

namespace App\Entity;

use App\Repository\UsuarioTriviaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsuarioTriviaRepository::class)]
#[ORM\Table(name: 'usuario_trivia')]
class UsuarioTrivia
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Trivia::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Trivia $trivia = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuario $usuario = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $puntajeTotal = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $completada = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->completada = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrivia(): ?Trivia
    {
        return $this->trivia;
    }

    public function setTrivia(?Trivia $trivia): static
    {
        $this->trivia = $trivia;

        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): static
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getPuntajeTotal(): ?int
    {
        return $this->puntajeTotal;
    }

    public function setPuntajeTotal(?int $puntajeTotal): static
    {
        $this->puntajeTotal = $puntajeTotal;

        return $this;
    }

    public function getCompletada(): ?bool
    {
        return $this->completada;
    }

    public function setCompletada(bool $completada): static
    {
        $this->completada = $completada;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
