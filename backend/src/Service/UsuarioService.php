<?php

namespace App\Service;

use App\DTO\Usuario;

class UsuarioService
{
    public function __construct(
        private ApiClient $apiClient
    ) {
    }

    public function findAll(): array
    {
        $data = $this->apiClient->get('/api/v1/usuarios');
        
        return array_map(
            fn(array $userData) => Usuario::fromArray($userData),
            $data
        );
    }

    public function findById(int $id): ?Usuario
    {
        try {
            $data = $this->apiClient->get('/api/v1/usuarios/' . $id);
            return Usuario::fromArray($data);
        } catch (\RuntimeException $e) {
            return null;
        }
    }

    public function create(Usuario $usuario): Usuario
    {
        $data = $this->apiClient->post('/api/v1/usuarios', $usuario->toArray());
        return Usuario::fromArray($data);
    }

    public function update(int $id, Usuario $usuario): Usuario
    {
        $data = $this->apiClient->put('/api/v1/usuarios/' . $id, $usuario->toArray());
        return Usuario::fromArray($data);
    }

    public function delete(int $id): void
    {
        $this->apiClient->delete('/api/v1/usuarios/' . $id);
    }
}
