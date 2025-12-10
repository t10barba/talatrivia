<?php

namespace App\Service;

class TriviaPreguntaService
{
    public function __construct(
        private ApiClient $apiClient
    ) {
    }

    public function findByTriviaId(int $triviaId): array
    {
        try {
            return $this->apiClient->get("/api/v1/trivias/{$triviaId}/preguntas");
        } catch (\Exception $e) {
            return [];
        }
    }

    public function bulkAssign(int $triviaId, array $preguntas): void
    {
        try {
            $result = $this->apiClient->post("/api/v1/trivias/{$triviaId}/preguntas/bulk", [
                'preguntas' => $preguntas
            ]);
            // Log para debug
            error_log('Bulk assign exitoso para trivia ' . $triviaId . ': ' . json_encode($result));
        } catch (\Exception $e) {
            // Log el error completo
            error_log('Error en bulkAssign: ' . $e->getMessage() . ' | Código: ' . $e->getCode() . ' | Trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }
}
