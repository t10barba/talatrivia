<?php

namespace App\DTO;

class Respuesta
{
    private ?int $id = null;
    private ?int $preguntaId = null;
    private ?string $texto = null;
    private ?bool $esCorrecta = null;
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

    public function getPreguntaId(): ?int
    {
        return $this->preguntaId;
    }

    public function setPreguntaId(?int $preguntaId): self
    {
        $this->preguntaId = $preguntaId;
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

    public function getEsCorrecta(): ?bool
    {
        return $this->esCorrecta;
    }

    public function setEsCorrecta(?bool $esCorrecta): self
    {
        $this->esCorrecta = $esCorrecta;
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
        $respuesta = new self();
        $respuesta->setId($data['id'] ?? null);
        $respuesta->setPreguntaId($data['preguntaId'] ?? null);
        $respuesta->setTexto($data['texto'] ?? null);
        $respuesta->setEsCorrecta($data['esCorrecta'] ?? null);
        
        if (isset($data['createdAt'])) {
            $respuesta->setCreatedAt(new \DateTimeImmutable($data['createdAt']));
        }
        
        return $respuesta;
    }

    public function toArray(): array
    {
        return [
            'preguntaId' => $this->preguntaId,
            'texto' => $this->texto,
            'esCorrecta' => $this->esCorrecta,
        ];
    }
}
