<?php

namespace App\Entity;

use App\Repository\PreguntaTriviaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PreguntaTriviaRepository::class)]
#[ORM\Table(name: 'pregunta_trivia')]
class PreguntaTrivia
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Trivia::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Trivia $trivia = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Pregunta::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Pregunta $pregunta = null;

    #[ORM\Column(type: 'integer')]
    private ?int $orden = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
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

    public function getPregunta(): ?Pregunta
    {
        return $this->pregunta;
    }

    public function setPregunta(?Pregunta $pregunta): static
    {
        $this->pregunta = $pregunta;

        return $this;
    }

    public function getOrden(): ?int
    {
        return $this->orden;
    }

    public function setOrden(int $orden): static
    {
        $this->orden = $orden;

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
