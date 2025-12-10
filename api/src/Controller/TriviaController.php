<?php

namespace App\Controller;

use App\Entity\Trivia;
use App\Repository\TriviaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/v1/trivias', name: 'api_trivias_')]
#[OA\Tag(name: 'Trivias')]
class TriviaController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TriviaRepository $triviaRepository
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    #[OA\Get(
        path: '/api/v1/trivias',
        summary: 'Listar todas las trivias',
        tags: ['Trivias']
    )]
    #[OA\Response(
        response: 200,
        description: 'Lista de trivias obtenida exitosamente',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(type: 'object', ref: new OA\Schema(ref: Trivia::class))
        )
    )]
    public function index(): JsonResponse
    {
        $trivias = $this->triviaRepository->findAll();
        
        $data = array_map(function (Trivia $trivia) {
            return $this->serializeTrivia($trivia);
        }, $trivias);

        return $this->json($data);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        path: '/api/v1/trivias/{id}',
        summary: 'Obtener una trivia por ID',
        tags: ['Trivias']
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID de la trivia',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Trivia encontrada',
        content: new OA\JsonContent(ref: Trivia::class)
    )]
    #[OA\Response(
        response: 404,
        description: 'Trivia no encontrada',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'error', type: 'string', example: 'Trivia no encontrada')
            ]
        )
    )]
    public function show(int $id): JsonResponse
    {
        $trivia = $this->triviaRepository->find($id);

        if (!$trivia) {
            return $this->json(['error' => 'Trivia no encontrada'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($this->serializeTrivia($trivia));
    }

    #[Route('', name: 'create', methods: ['POST'])]
    #[OA\Post(
        path: '/api/v1/trivias',
        summary: 'Crear una nueva trivia',
        tags: ['Trivias']
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['nombre', 'descripcion'],
            properties: [
                new OA\Property(property: 'nombre', type: 'string', example: 'Trivia de Historia'),
                new OA\Property(property: 'descripcion', type: 'string', example: 'Preguntas sobre historia universal')
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Trivia creada exitosamente',
        content: new OA\JsonContent(ref: Trivia::class)
    )]
    #[OA\Response(
        response: 400,
        description: 'Datos inválidos o incompletos',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'error', type: 'string', example: 'Los campos nombre y descripcion son requeridos')
            ]
        )
    )]
    #[OA\Response(
        response: 409,
        description: 'El nombre ya está registrado',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'error', type: 'string', example: 'El nombre ya está registrado')
            ]
        )
    )]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['nombre']) || !isset($data['descripcion'])) {
            return $this->json(
                ['error' => 'Los campos nombre y descripcion son requeridos'],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Verificar si el nombre ya existe
        $existingTrivia = $this->triviaRepository->findOneBy(['nombre' => $data['nombre']]);
        if ($existingTrivia) {
            return $this->json(
                ['error' => 'El nombre ya está registrado'],
                Response::HTTP_CONFLICT
            );
        }

        $trivia = new Trivia();
        $trivia->setNombre($data['nombre']);
        $trivia->setDescripcion($data['descripcion']);

        $this->entityManager->persist($trivia);
        $this->entityManager->flush();

        return $this->json(
            $this->serializeTrivia($trivia),
            Response::HTTP_CREATED
        );
    }

    #[Route('/{id}', name: 'update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(
        path: '/api/v1/trivias/{id}',
        summary: 'Actualizar una trivia',
        tags: ['Trivias']
    )]
    #[OA\Patch(
        path: '/api/v1/trivias/{id}',
        summary: 'Actualizar parcialmente una trivia',
        tags: ['Trivias']
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID de la trivia',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'nombre', type: 'string', example: 'Trivia de Historia'),
                new OA\Property(property: 'descripcion', type: 'string', example: 'Preguntas sobre historia universal')
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Trivia actualizada exitosamente',
        content: new OA\JsonContent(ref: Trivia::class)
    )]
    #[OA\Response(
        response: 404,
        description: 'Trivia no encontrada'
    )]
    #[OA\Response(
        response: 409,
        description: 'El nombre ya está registrado'
    )]
    public function update(int $id, Request $request): JsonResponse
    {
        $trivia = $this->triviaRepository->find($id);

        if (!$trivia) {
            return $this->json(['error' => 'Trivia no encontrada'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['nombre'])) {
            // Verificar si el nombre ya existe en otra trivia
            $existingTrivia = $this->triviaRepository->findOneBy(['nombre' => $data['nombre']]);
            if ($existingTrivia && $existingTrivia->getId() !== $trivia->getId()) {
                return $this->json(
                    ['error' => 'El nombre ya está registrado'],
                    Response::HTTP_CONFLICT
                );
            }
            $trivia->setNombre($data['nombre']);
        }

        if (isset($data['descripcion'])) {
            $trivia->setDescripcion($data['descripcion']);
        }

        $this->entityManager->flush();

        return $this->json($this->serializeTrivia($trivia));
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/v1/trivias/{id}',
        summary: 'Eliminar una trivia',
        tags: ['Trivias']
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID de la trivia',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 204,
        description: 'Trivia eliminada exitosamente'
    )]
    #[OA\Response(
        response: 404,
        description: 'Trivia no encontrada'
    )]
    public function delete(int $id): JsonResponse
    {
        $trivia = $this->triviaRepository->find($id);

        if (!$trivia) {
            return $this->json(['error' => 'Trivia no encontrada'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($trivia);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    private function serializeTrivia(Trivia $trivia): array
    {
        return [
            'id' => $trivia->getId(),
            'nombre' => $trivia->getNombre(),
            'descripcion' => $trivia->getDescripcion(),
            'createdAt' => $trivia->getCreatedAt()?->format('Y-m-d H:i:s'),
        ];
    }
}
