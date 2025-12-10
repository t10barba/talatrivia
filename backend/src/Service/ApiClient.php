<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;

class ApiClient
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $apiBaseUrl
    ) {
    }

    public function get(string $endpoint): array
    {
        try {
            $response = $this->httpClient->request('GET', $this->apiBaseUrl . $endpoint);
            return $response->toArray();
        } catch (ClientExceptionInterface | ServerExceptionInterface $e) {
            $this->handleHttpException($e);
        } catch (TransportExceptionInterface $e) {
            throw new \RuntimeException('Error connecting to API: ' . $e->getMessage());
        }
    }

    public function post(string $endpoint, array $data): array
    {
        try {
            $response = $this->httpClient->request('POST', $this->apiBaseUrl . $endpoint, [
                'json' => $data,
            ]);
            return $response->toArray();
        } catch (ClientExceptionInterface | ServerExceptionInterface $e) {
            $this->handleHttpException($e);
        } catch (TransportExceptionInterface $e) {
            throw new \RuntimeException('Error connecting to API: ' . $e->getMessage());
        }
    }

    public function put(string $endpoint, array $data): array
    {
        try {
            $response = $this->httpClient->request('PUT', $this->apiBaseUrl . $endpoint, [
                'json' => $data,
            ]);
            return $response->toArray();
        } catch (ClientExceptionInterface | ServerExceptionInterface $e) {
            $this->handleHttpException($e);
        } catch (TransportExceptionInterface $e) {
            throw new \RuntimeException('Error connecting to API: ' . $e->getMessage());
        }
    }

    public function delete(string $endpoint): void
    {
        try {
            $this->httpClient->request('DELETE', $this->apiBaseUrl . $endpoint);
        } catch (ClientExceptionInterface | ServerExceptionInterface $e) {
            $this->handleHttpException($e);
        } catch (TransportExceptionInterface $e) {
            throw new \RuntimeException('Error connecting to API: ' . $e->getMessage());
        }
    }

    private function handleHttpException(ClientExceptionInterface | ServerExceptionInterface $e): never
    {
        $response = $e->getResponse();
        $statusCode = $response->getStatusCode();
        
        try {
            $errorData = $response->toArray(false);
            $errorMessage = $errorData['error'] ?? $errorData['message'] ?? 'Error desconocido';
        } catch (\Exception $decodeException) {
            // Si no se puede decodificar, intentar obtener el contenido raw
            try {
                $content = $response->getContent(false);
                $errorMessage = 'Error HTTP ' . $statusCode . ': ' . substr($content, 0, 200);
            } catch (\Exception $contentException) {
                $errorMessage = 'Error HTTP ' . $statusCode . ' (no se pudo obtener detalle)';
            }
        }

        throw new \RuntimeException($errorMessage, $statusCode);
    }
}
