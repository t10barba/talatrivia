<?php

namespace App\Entity;

use App\Repository\RespuestaUsuarioRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RespuestaUsuarioRepository::class)]
#[ORM\Table(name: 'respuesta_usuario')]
class RespuestaUsuario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: UsuarioTrivia::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?UsuarioTrivia $usuarioTrivia = null;

    #[ORM\ManyToOne(targetEntity: Pregunta::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Pregunta $pregunta = null;

    #[ORM\ManyToOne(targetEntity: Respuesta::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Respuesta $respuesta = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $esCorrecta = null;

    #[ORM\Column(type: 'integer')]
    private ?int $puntajeObtenido = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuarioTrivia(): ?UsuarioTrivia
    {
        return $this->usuarioTrivia;
    }

    public function setUsuarioTrivia(?UsuarioTrivia $usuarioTrivia): static
    {
        $this->usuarioTrivia = $usuarioTrivia;

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

    public function getRespuesta(): ?Respuesta
    {
        return $this->respuesta;
    }

    public function setRespuesta(?Respuesta $respuesta): static
    {
        $this->respuesta = $respuesta;

        return $this;
    }

    public function getEsCorrecta(): ?bool
    {
        return $this->esCorrecta;
    }

    public function setEsCorrecta(bool $esCorrecta): static
    {
        $this->esCorrecta = $esCorrecta;

        return $this;
    }

    public function getPuntajeObtenido(): ?int
    {
        return $this->puntajeObtenido;
    }

    public function setPuntajeObtenido(int $puntajeObtenido): static
    {
        $this->puntajeObtenido = $puntajeObtenido;

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
