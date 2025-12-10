<?php

namespace App\Service;

use App\DTO\Pregunta;

class PreguntaService
{
    public function __construct(
        private ApiClient $apiClient
    ) {
    }

    public function findAll(): array
    {
        $data = $this->apiClient->get('/api/v1/preguntas');
        
        return array_map(
            fn(array $preguntaData) => Pregunta::fromArray($preguntaData),
            $data
        );
    }

    public function findById(int $id): ?Pregunta
    {
        try {
            $data = $this->apiClient->get('/api/v1/preguntas/' . $id);
            return Pregunta::fromArray($data);
        } catch (\RuntimeException $e) {
            return null;
        }
    }

    public function create(Pregunta $pregunta): Pregunta
    {
        $data = $this->apiClient->post('/api/v1/preguntas', $pregunta->toArray());
        return Pregunta::fromArray($data);
    }

    public function update(int $id, Pregunta $pregunta): Pregunta
    {
        $data = $this->apiClient->put('/api/v1/preguntas/' . $id, $pregunta->toArray());
        return Pregunta::fromArray($data);
    }

    public function delete(int $id): void
    {
        $this->apiClient->delete('/api/v1/preguntas/' . $id);
    }
}
