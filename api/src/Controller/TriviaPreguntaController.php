<?php

namespace App\Controller;

use App\Entity\PreguntaTrivia;
use App\Repository\PreguntaTriviaRepository;
use App\Repository\TriviaRepository;
use App\Repository\PreguntaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/v1/trivias/{triviaId}/preguntas', name: 'api_trivia_preguntas_')]
#[OA\Tag(name: 'Trivia Preguntas')]
class TriviaPreguntaController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PreguntaTriviaRepository $preguntaTriviaRepository,
        private TriviaRepository $triviaRepository,
        private PreguntaRepository $preguntaRepository
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    #[OA\Get(
        path: '/api/v1/trivias/{triviaId}/preguntas',
        summary: 'Listar preguntas de una trivia',
        tags: ['Trivia Preguntas']
    )]
    #[OA\Parameter(
        name: 'triviaId',
        in: 'path',
        required: true,
        description: 'ID de la trivia',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Lista de preguntas de la trivia',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(type: 'object')
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Trivia no encontrada'
    )]
    public function index(int $triviaId): JsonResponse
    {
        $trivia = $this->triviaRepository->find($triviaId);
        if (!$trivia) {
            return $this->json(['error' => 'Trivia no encontrada'], Response::HTTP_NOT_FOUND);
        }

        $preguntasTrivia = $this->preguntaTriviaRepository->findBy(
            ['trivia' => $triviaId],
            ['orden' => 'ASC']
        );

        $data = array_map(function (PreguntaTrivia $preguntaTrivia) {
            return $this->serializePreguntaTrivia($preguntaTrivia);
        }, $preguntasTrivia);

        return $this->json($data);
    }

    #[Route('', name: 'add', methods: ['POST'])]
    #[OA\Post(
        path: '/api/v1/trivias/{triviaId}/preguntas',
        summary: 'Agregar pregunta a una trivia',
        tags: ['Trivia Preguntas']
    )]
    #[OA\Parameter(
        name: 'triviaId',
        in: 'path',
        required: true,
        description: 'ID de la trivia',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['preguntaId', 'orden'],
            properties: [
                new OA\Property(property: 'preguntaId', type: 'integer', example: 1),
                new OA\Property(property: 'orden', type: 'integer', example: 1, description: 'Orden de la pregunta en la trivia')
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Pregunta agregada a la trivia exitosamente'
    )]
    #[OA\Response(
        response: 404,
        description: 'Trivia o pregunta no encontrada'
    )]
    #[OA\Response(
        response: 409,
        description: 'La pregunta ya está asociada a esta trivia'
    )]
    public function add(int $triviaId, Request $request): JsonResponse
    {
        $trivia = $this->triviaRepository->find($triviaId);
        if (!$trivia) {
            return $this->json(['error' => 'Trivia no encontrada'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['preguntaId']) || !isset($data['orden'])) {
            return $this->json(
                ['error' => 'Los campos preguntaId y orden son requeridos'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $pregunta = $this->preguntaRepository->find($data['preguntaId']);
        if (!$pregunta) {
            return $this->json(['error' => 'Pregunta no encontrada'], Response::HTTP_NOT_FOUND);
        }

        // Verificar si la pregunta ya está asociada
        $existente = $this->preguntaTriviaRepository->findOneBy([
            'trivia' => $triviaId,
            'pregunta' => $data['preguntaId']
        ]);

        if ($existente) {
            return $this->json(
                ['error' => 'La pregunta ya está asociada a esta trivia'],
                Response::HTTP_CONFLICT
            );
        }

        $preguntaTrivia = new PreguntaTrivia();
        $preguntaTrivia->setTrivia($trivia);
        $preguntaTrivia->setPregunta($pregunta);
        $preguntaTrivia->setOrden((int)$data['orden']);

        $this->entityManager->persist($preguntaTrivia);
        $this->entityManager->flush();

        return $this->json(
            $this->serializePreguntaTrivia($preguntaTrivia),
            Response::HTTP_CREATED
        );
    }

    #[Route('/bulk', name: 'bulk_assign', methods: ['POST'])]
    #[OA\Post(
        path: '/api/v1/trivias/{triviaId}/preguntas/bulk',
        summary: 'Asignar múltiples preguntas a una trivia',
        tags: ['Trivia Preguntas']
    )]
    #[OA\Parameter(
        name: 'triviaId',
        in: 'path',
        required: true,
        description: 'ID de la trivia',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['preguntas'],
            properties: [
                new OA\Property(
                    property: 'preguntas',
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'preguntaId', type: 'integer'),
                            new OA\Property(property: 'orden', type: 'integer')
                        ]
                    ),
                    example: [
                        ['preguntaId' => 1, 'orden' => 1],
                        ['preguntaId' => 2, 'orden' => 2]
                    ]
                )
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Preguntas asignadas exitosamente'
    )]
    public function bulkAssign(int $triviaId, Request $request): JsonResponse
    {
        $trivia = $this->triviaRepository->find($triviaId);
        if (!$trivia) {
            return $this->json(['error' => 'Trivia no encontrada'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['preguntas']) || !is_array($data['preguntas'])) {
            return $this->json(
                ['error' => 'El campo preguntas es requerido y debe ser un array'],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Eliminar todas las preguntas actuales de la trivia
        $preguntasActuales = $this->preguntaTriviaRepository->findBy(['trivia' => $triviaId]);
        foreach ($preguntasActuales as $preguntaTrivia) {
            $this->entityManager->remove($preguntaTrivia);
        }
        
        // Flush para eliminar antes de agregar nuevas
        $this->entityManager->flush();

        // Agregar las nuevas preguntas
        $resultado = [];
        foreach ($data['preguntas'] as $item) {
            if (!isset($item['preguntaId']) || !isset($item['orden'])) {
                continue;
            }

            $pregunta = $this->preguntaRepository->find($item['preguntaId']);
            if (!$pregunta) {
                continue;
            }

            $preguntaTrivia = new PreguntaTrivia();
            $preguntaTrivia->setTrivia($trivia);
            $preguntaTrivia->setPregunta($pregunta);
            $preguntaTrivia->setOrden((int)$item['orden']);

            $this->entityManager->persist($preguntaTrivia);
            $resultado[] = $this->serializePreguntaTrivia($preguntaTrivia);
        }

        $this->entityManager->flush();

        return $this->json([
            'message' => 'Preguntas asignadas exitosamente',
            'preguntas' => $resultado
        ]);
    }

    #[Route('/{preguntaId}', name: 'update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(
        path: '/api/v1/trivias/{triviaId}/preguntas/{preguntaId}',
        summary: 'Actualizar orden de pregunta en trivia',
        tags: ['Trivia Preguntas']
    )]
    #[OA\Patch(
        path: '/api/v1/trivias/{triviaId}/preguntas/{preguntaId}',
        summary: 'Actualizar orden de pregunta en trivia',
        tags: ['Trivia Preguntas']
    )]
    #[OA\Parameter(
        name: 'triviaId',
        in: 'path',
        required: true,
        description: 'ID de la trivia',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Parameter(
        name: 'preguntaId',
        in: 'path',
        required: true,
        description: 'ID de la pregunta',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['orden'],
            properties: [
                new OA\Property(property: 'orden', type: 'integer', example: 2)
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Orden actualizado exitosamente'
    )]
    #[OA\Response(
        response: 404,
        description: 'Relación no encontrada'
    )]
    public function update(int $triviaId, int $preguntaId, Request $request): JsonResponse
    {
        $preguntaTrivia = $this->preguntaTriviaRepository->findOneBy([
            'trivia' => $triviaId,
            'pregunta' => $preguntaId
        ]);

        if (!$preguntaTrivia) {
            return $this->json(
                ['error' => 'Pregunta no encontrada en esta trivia'],
                Response::HTTP_NOT_FOUND
            );
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['orden'])) {
            $preguntaTrivia->setOrden((int)$data['orden']);
        }

        $this->entityManager->flush();

        return $this->json($this->serializePreguntaTrivia($preguntaTrivia));
    }

    #[Route('/{preguntaId}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/v1/trivias/{triviaId}/preguntas/{preguntaId}',
        summary: 'Eliminar pregunta de una trivia',
        tags: ['Trivia Preguntas']
    )]
    #[OA\Parameter(
        name: 'triviaId',
        in: 'path',
        required: true,
        description: 'ID de la trivia',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Parameter(
        name: 'preguntaId',
        in: 'path',
        required: true,
        description: 'ID de la pregunta',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 204,
        description: 'Pregunta eliminada de la trivia exitosamente'
    )]
    #[OA\Response(
        response: 404,
        description: 'Relación no encontrada'
    )]
    public function delete(int $triviaId, int $preguntaId): JsonResponse
    {
        $preguntaTrivia = $this->preguntaTriviaRepository->findOneBy([
            'trivia' => $triviaId,
            'pregunta' => $preguntaId
        ]);

        if (!$preguntaTrivia) {
            return $this->json(
                ['error' => 'Pregunta no encontrada en esta trivia'],
                Response::HTTP_NOT_FOUND
            );
        }

        $this->entityManager->remove($preguntaTrivia);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    private function serializePreguntaTrivia(PreguntaTrivia $preguntaTrivia): array
    {
        $pregunta = $preguntaTrivia->getPregunta();
        
        return [
            'preguntaId' => $pregunta?->getId(),
            'texto' => $pregunta?->getTexto(),
            'dificultad' => $pregunta?->getDificultad(),
            'orden' => $preguntaTrivia->getOrden(),
            'createdAt' => $preguntaTrivia->getCreatedAt()?->format('Y-m-d H:i:s'),
        ];
    }
}
