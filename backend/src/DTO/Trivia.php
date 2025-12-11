<?php

namespace App\DTO;

class Trivia
{
    private ?int $id = null;
    private ?string $slug = null;
    private ?string $nombre = null;
    private ?string $descripcion = null;
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;
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

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;
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

    public static function fromArray(array $data): self
    {
        $trivia = new self();
        $trivia->setId($data['id'] ?? null);
        $trivia->setSlug($data['slug'] ?? null);
        $trivia->setNombre($data['nombre'] ?? null);
        $trivia->setDescripcion($data['descripcion'] ?? null);
        
        if (isset($data['createdAt'])) {
            $trivia->setCreatedAt(new \DateTimeImmutable($data['createdAt']));
        }
        
        return $trivia;
    }

    public function toArray(): array
    {
        return [
            'slug' => $this->slug,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
        ];
    }
}
