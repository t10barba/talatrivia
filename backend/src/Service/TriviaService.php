<?php

namespace App\Service;

use App\DTO\Trivia;

class TriviaService
{
    public function __construct(
        private ApiClient $apiClient
    ) {
    }

    public function findAll(): array
    {
        $data = $this->apiClient->get('/api/v1/trivias');
        
        return array_map(
            fn(array $triviaData) => Trivia::fromArray($triviaData),
            $data
        );
    }

    public function findById(int $id): ?Trivia
    {
        try {
            $data = $this->apiClient->get('/api/v1/trivias/' . $id);
            return Trivia::fromArray($data);
        } catch (\RuntimeException $e) {
            return null;
        }
    }

    public function create(Trivia $trivia): Trivia
    {
        $data = $this->apiClient->post('/api/v1/trivias', $trivia->toArray());
        return Trivia::fromArray($data);
    }

    public function update(int $id, Trivia $trivia): Trivia
    {
        $data = $this->apiClient->put('/api/v1/trivias/' . $id, $trivia->toArray());
        return Trivia::fromArray($data);
    }

    public function delete(int $id): void
    {
        $this->apiClient->delete('/api/v1/trivias/' . $id);
    }
}
