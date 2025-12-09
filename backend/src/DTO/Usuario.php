<?php

namespace App\DTO;

class Usuario
{
    private ?int $id = null;
    private ?string $nombre = null;
    private ?string $apellido = null;
    private ?string $email = null;
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): self
    {
        $this->nombre = $nombre;
        return $this;
    }

    public function getApellido(): ?string
    {
        return $this->apellido;
    }

    public function setApellido(?string $apellido): self
    {
        $this->apellido = $apellido;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getNombreCompleto(): string
    {
        return trim(($this->nombre ?? '') . ' ' . ($this->apellido ?? ''));
    }

    public static function fromArray(array $data): self
    {
        $usuario = new self();
        $usuario->setId($data['id'] ?? null);
        $usuario->setNombre($data['nombre'] ?? null);
        $usuario->setApellido($data['apellido'] ?? null);
        $usuario->setEmail($data['email'] ?? null);
        
        if (isset($data['createdAt'])) {
            $usuario->setCreatedAt(new \DateTimeImmutable($data['createdAt']));
        }
        
        return $usuario;
    }

    public function toArray(): array
    {
        return [
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'email' => $this->email,
        ];
    }
}
