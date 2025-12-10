<?php

namespace App\DTO;

class Pregunta
{
    private ?int $id = null;
    private ?string $texto = null;
    private ?int $dificultad = null;
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

    public function getTexto(): ?string
    {
        return $this->texto;
    }

    public function setTexto(?string $texto): self
    {
        $this->texto = $texto;
        return $this;
    }

    public function getDificultad(): ?int
    {
        return $this->dificultad;
    }

    public function setDificultad(?int $dificultad): self
    {
        $this->dificultad = $dificultad;
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
        $pregunta = new self();
        $pregunta->setId($data['id'] ?? null);
        $pregunta->setTexto($data['texto'] ?? null);
        $pregunta->setDificultad($data['dificultad'] ?? null);
        
        if (isset($data['createdAt'])) {
            $pregunta->setCreatedAt(new \DateTimeImmutable($data['createdAt']));
        }
        
        return $pregunta;
    }

    public function toArray(): array
    {
        return [
            'texto' => $this->texto,
            'dificultad' => $this->dificultad,
        ];
    }
}
