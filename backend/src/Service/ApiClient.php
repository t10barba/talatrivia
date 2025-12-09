<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ApiClient
{
    private string $apiBaseUrl;

    public function __construct(
        private HttpClientInterface $httpClient,
        string $apiBaseUrl = 'http://api_nginx:80'
    ) {
        $this->apiBaseUrl = $apiBaseUrl;
    }

    public function get(string $endpoint): array
    {
        try {
            $response = $this->httpClient->request('GET', $this->apiBaseUrl . $endpoint);
            return $response->toArray();
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
        } catch (TransportExceptionInterface $e) {
            throw new \RuntimeException('Error connecting to API: ' . $e->getMessage());
        }
    }

    public function delete(string $endpoint): void
    {
        try {
            $this->httpClient->request('DELETE', $this->apiBaseUrl . $endpoint);
        } catch (TransportExceptionInterface $e) {
            throw new \RuntimeException('Error connecting to API: ' . $e->getMessage());
        }
    }
}
