<?php

namespace App\Service;

use App\DTO\Respuesta;

class RespuestaService
{
    public function __construct(
        private ApiClient $apiClient
    ) {
    }

    public function findAll(): array
    {
        $data = $this->apiClient->get('/api/v1/respuestas');
        
        return array_map(
            fn(array $respuestaData) => Respuesta::fromArray($respuestaData),
            $data
        );
    }

    public function findByPreguntaId(int $preguntaId): array
    {
        $data = $this->apiClient->get('/api/v1/respuestas?preguntaId=' . $preguntaId);
        
        return array_map(
            fn(array $respuestaData) => Respuesta::fromArray($respuestaData),
            $data
        );
    }

    public function findById(int $id): ?Respuesta
    {
        try {
            $data = $this->apiClient->get('/api/v1/respuestas/' . $id);
            return Respuesta::fromArray($data);
        } catch (\RuntimeException $e) {
            return null;
        }
    }

    public function create(Respuesta $respuesta): Respuesta
    {
        $data = $this->apiClient->post('/api/v1/respuestas', $respuesta->toArray());
        return Respuesta::fromArray($data);
    }

    public function update(int $id, Respuesta $respuesta): Respuesta
    {
        $data = $this->apiClient->put('/api/v1/respuestas/' . $id, $respuesta->toArray());
        return Respuesta::fromArray($data);
    }

    public function delete(int $id): void
    {
        $this->apiClient->delete('/api/v1/respuestas/' . $id);
    }
}
