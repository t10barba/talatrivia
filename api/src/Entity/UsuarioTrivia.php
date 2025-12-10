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

    #[ORM\Column(type: 'smallint', options: ['default' => 0])]
    private int $estado = 0;

    #[ORM\Column(type: 'string', length: 6, unique: true)]
    private ?string $codigo = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $startedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $finishedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->estado = 0;
        $this->codigo = $this->generateCodigo();
    }

    private function generateCodigo(): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $codigo = '';
        for ($i = 0; $i < 6; $i++) {
            $codigo .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $codigo;
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

    public function getEstado(): int
    {
        return $this->estado;
    }

    public function setEstado(int $estado): static
    {
        $this->estado = $estado;

        return $this;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): static
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getStartedAt(): ?\DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function setStartedAt(?\DateTimeImmutable $startedAt): static
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getFinishedAt(): ?\DateTimeImmutable
    {
        return $this->finishedAt;
    }

    public function setFinishedAt(?\DateTimeImmutable $finishedAt): static
    {
        $this->finishedAt = $finishedAt;

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
