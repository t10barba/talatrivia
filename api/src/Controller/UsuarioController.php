<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/v1/usuarios', name: 'api_usuarios_')]
#[OA\Tag(name: 'Usuarios')]
class UsuarioController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UsuarioRepository $usuarioRepository
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    #[OA\Get(
        path: '/api/v1/usuarios',
        summary: 'Listar todos los usuarios',
        tags: ['Usuarios']
    )]
    #[OA\Response(
        response: 200,
        description: 'Lista de usuarios obtenida exitosamente',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(type: 'object', ref: new OA\Schema(ref: Usuario::class))
        )
    )]
    public function index(): JsonResponse
    {
        $usuarios = $this->usuarioRepository->findAll();
        
        $data = array_map(function (Usuario $usuario) {
            return $this->serializeUsuario($usuario);
        }, $usuarios);

        return $this->json($data);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        path: '/api/v1/usuarios/{id}',
        summary: 'Obtener un usuario por ID',
        tags: ['Usuarios']
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID del usuario',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Usuario encontrado',
        content: new OA\JsonContent(ref: Usuario::class)
    )]
    #[OA\Response(
        response: 404,
        description: 'Usuario no encontrado',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'error', type: 'string', example: 'Usuario no encontrado')
            ]
        )
    )]
    public function show(int $id): JsonResponse
    {
        $usuario = $this->usuarioRepository->find($id);

        if (!$usuario) {
            return $this->json(['error' => 'Usuario no encontrado'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($this->serializeUsuario($usuario));
    }

    #[Route('', name: 'create', methods: ['POST'])]
    #[OA\Post(
        path: '/api/v1/usuarios',
        summary: 'Crear un nuevo usuario',
        tags: ['Usuarios']
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['nombre', 'apellido', 'email'],
            properties: [
                new OA\Property(property: 'nombre', type: 'string', example: 'Juan'),
                new OA\Property(property: 'apellido', type: 'string', example: 'Pérez'),
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'juan.perez@example.com')
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Usuario creado exitosamente',
        content: new OA\JsonContent(ref: Usuario::class)
    )]
    #[OA\Response(
        response: 400,
        description: 'Datos inválidos o incompletos',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'error', type: 'string', example: 'Los campos nombre, apellido y email son requeridos')
            ]
        )
    )]
    #[OA\Response(
        response: 409,
        description: 'El email ya está registrado',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'error', type: 'string', example: 'El email ya está registrado')
            ]
        )
    )]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['nombre']) || !isset($data['apellido']) || !isset($data['email'])) {
            return $this->json(
                ['error' => 'Los campos nombre, apellido y email son requeridos'],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Verificar si el email ya existe
        $existingUsuario = $this->usuarioRepository->findOneBy(['email' => $data['email']]);
        if ($existingUsuario) {
            return $this->json(
                ['error' => 'El email ya está registrado'],
                Response::HTTP_CONFLICT
            );
        }

        $usuario = new Usuario();
        $usuario->setNombre($data['nombre']);
        $usuario->setApellido($data['apellido']);
        $usuario->setEmail($data['email']);

        $this->entityManager->persist($usuario);
        $this->entityManager->flush();

        return $this->json(
            $this->serializeUsuario($usuario),
            Response::HTTP_CREATED
        );
    }

    #[Route('/{id}', name: 'update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(
        path: '/api/v1/usuarios/{id}',
        summary: 'Actualizar un usuario',
        tags: ['Usuarios']
    )]
    #[OA\Patch(
        path: '/api/v1/usuarios/{id}',
        summary: 'Actualizar parcialmente un usuario',
        tags: ['Usuarios']
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID del usuario',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'nombre', type: 'string', example: 'Juan'),
                new OA\Property(property: 'apellido', type: 'string', example: 'Pérez'),
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'juan.perez@example.com')
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Usuario actualizado exitosamente',
        content: new OA\JsonContent(ref: Usuario::class)
    )]
    #[OA\Response(
        response: 404,
        description: 'Usuario no encontrado'
    )]
    #[OA\Response(
        response: 409,
        description: 'El email ya está registrado'
    )]
    public function update(int $id, Request $request): JsonResponse
    {
        $usuario = $this->usuarioRepository->find($id);

        if (!$usuario) {
            return $this->json(['error' => 'Usuario no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['nombre'])) {
            $usuario->setNombre($data['nombre']);
        }

        if (isset($data['apellido'])) {
            $usuario->setApellido($data['apellido']);
        }

        if (isset($data['email'])) {
            // Verificar si el email ya existe en otro usuario
            $existingUsuario = $this->usuarioRepository->findOneBy(['email' => $data['email']]);
            if ($existingUsuario && $existingUsuario->getId() !== $usuario->getId()) {
                return $this->json(
                    ['error' => 'El email ya está registrado'],
                    Response::HTTP_CONFLICT
                );
            }
            $usuario->setEmail($data['email']);
        }

        $this->entityManager->flush();

        return $this->json($this->serializeUsuario($usuario));
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/v1/usuarios/{id}',
        summary: 'Eliminar un usuario',
        tags: ['Usuarios']
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID del usuario',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 204,
        description: 'Usuario eliminado exitosamente'
    )]
    #[OA\Response(
        response: 404,
        description: 'Usuario no encontrado'
    )]
    public function delete(int $id): JsonResponse
    {
        $usuario = $this->usuarioRepository->find($id);

        if (!$usuario) {
            return $this->json(['error' => 'Usuario no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($usuario);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    private function serializeUsuario(Usuario $usuario): array
    {
        return [
            'id' => $usuario->getId(),
            'nombre' => $usuario->getNombre(),
            'apellido' => $usuario->getApellido(),
            'email' => $usuario->getEmail(),
            'createdAt' => $usuario->getCreatedAt()?->format('Y-m-d H:i:s'),
        ];
    }
}
