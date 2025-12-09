<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
#[ORM\Table(name: 'usuario')]
#[OA\Schema(
    schema: 'Usuario',
    title: 'Usuario',
    description: 'Entidad Usuario del sistema',
    required: ['id', 'nombre', 'apellido', 'email', 'createdAt']
)]
class Usuario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[OA\Property(description: 'ID único del usuario', example: 1)]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[OA\Property(description: 'Nombre del usuario', maxLength: 255, example: 'Juan')]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    #[OA\Property(description: 'Apellido del usuario', maxLength: 255, example: 'Pérez')]
    private ?string $apellido = null;

    #[ORM\Column(length: 255, unique: true)]
    #[OA\Property(description: 'Email único del usuario', format: 'email', maxLength: 255, example: 'juan.perez@example.com')]
    private ?string $email = null;

    #[ORM\Column]
    #[OA\Property(description: 'Fecha y hora de creación del usuario', example: '2025-12-09 10:30:00')]
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

    public function getApellido(): ?string
    {
        return $this->apellido;
    }

    public function setApellido(string $apellido): static
    {
        $this->apellido = $apellido;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

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
