<?php

namespace App\Entity;

use App\Repository\TriviaRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: TriviaRepository::class)]
#[ORM\Table(name: 'trivia')]
#[OA\Schema(
    schema: 'Trivia',
    title: 'Trivia',
    description: 'Entidad Trivia del sistema',
    required: ['id', 'nombre', 'descripcion', 'createdAt']
)]
class Trivia
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[OA\Property(description: 'ID único de la trivia', example: 1)]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[OA\Property(description: 'Nombre de la trivia', maxLength: 255, example: 'Trivia de Historia')]
    private ?string $nombre = null;

    #[ORM\Column(type: 'text')]
    #[OA\Property(description: 'Descripción de la trivia', example: 'Preguntas sobre historia universal')]
    private ?string $descripcion = null;

    #[ORM\Column]
    #[OA\Property(description: 'Fecha y hora de creación de la trivia', example: '2025-12-10 15:30:00')]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): static
    {
        $this->descripcion = $descripcion;

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
