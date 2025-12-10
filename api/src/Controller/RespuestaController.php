<?php

namespace App\Controller;

use App\Entity\Respuesta;
use App\Repository\RespuestaRepository;
use App\Repository\PreguntaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/v1/respuestas', name: 'api_respuestas_')]
#[OA\Tag(name: 'Respuestas')]
class RespuestaController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RespuestaRepository $respuestaRepository,
        private PreguntaRepository $preguntaRepository
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    #[OA\Get(
        path: '/api/v1/respuestas',
        summary: 'Listar todas las respuestas',
        tags: ['Respuestas']
    )]
    #[OA\Parameter(
        name: 'preguntaId',
        in: 'query',
        required: false,
        description: 'Filtrar respuestas por ID de pregunta',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Lista de respuestas obtenida exitosamente',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(type: 'object', ref: new OA\Schema(ref: Respuesta::class))
        )
    )]
    public function index(Request $request): JsonResponse
    {
        $preguntaId = $request->query->get('preguntaId');
        
        if ($preguntaId) {
            $respuestas = $this->respuestaRepository->findBy(['pregunta' => $preguntaId]);
        } else {
            $respuestas = $this->respuestaRepository->findAll();
        }
        
        $data = array_map(function (Respuesta $respuesta) {
            return $this->serializeRespuesta($respuesta);
        }, $respuestas);

        return $this->json($data);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        path: '/api/v1/respuestas/{id}',
        summary: 'Obtener una respuesta por ID',
        tags: ['Respuestas']
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID de la respuesta',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Respuesta encontrada',
        content: new OA\JsonContent(ref: Respuesta::class)
    )]
    #[OA\Response(
        response: 404,
        description: 'Respuesta no encontrada',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'error', type: 'string', example: 'Respuesta no encontrada')
            ]
        )
    )]
    public function show(int $id): JsonResponse
    {
        $respuesta = $this->respuestaRepository->find($id);

        if (!$respuesta) {
            return $this->json(['error' => 'Respuesta no encontrada'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($this->serializeRespuesta($respuesta));
    }

    #[Route('', name: 'create', methods: ['POST'])]
    #[OA\Post(
        path: '/api/v1/respuestas',
        summary: 'Crear una nueva respuesta',
        tags: ['Respuestas']
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['preguntaId', 'texto', 'esCorrecta'],
            properties: [
                new OA\Property(property: 'preguntaId', type: 'integer', example: 1),
                new OA\Property(property: 'texto', type: 'string', example: 'París'),
                new OA\Property(property: 'esCorrecta', type: 'boolean', example: true)
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Respuesta creada exitosamente',
        content: new OA\JsonContent(ref: Respuesta::class)
    )]
    #[OA\Response(
        response: 400,
        description: 'Datos inválidos o incompletos',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'error', type: 'string', example: 'Los campos preguntaId, texto y esCorrecta son requeridos')
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Pregunta no encontrada',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'error', type: 'string', example: 'Pregunta no encontrada')
            ]
        )
    )]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['preguntaId']) || !isset($data['texto']) || !isset($data['esCorrecta'])) {
            return $this->json(
                ['error' => 'Los campos preguntaId, texto y esCorrecta son requeridos'],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Buscar la pregunta
        $pregunta = $this->preguntaRepository->find($data['preguntaId']);
        if (!$pregunta) {
            return $this->json(
                ['error' => 'Pregunta no encontrada'],
                Response::HTTP_NOT_FOUND
            );
        }

        // Validar que esCorrecta sea un booleano
        if (!is_bool($data['esCorrecta'])) {
            return $this->json(
                ['error' => 'El campo esCorrecta debe ser un valor booleano'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $respuesta = new Respuesta();
        $respuesta->setPregunta($pregunta);
        $respuesta->setTexto($data['texto']);
        $respuesta->setEsCorrecta($data['esCorrecta']);

        $this->entityManager->persist($respuesta);
        $this->entityManager->flush();

        return $this->json(
            $this->serializeRespuesta($respuesta),
            Response::HTTP_CREATED
        );
    }

    #[Route('/{id}', name: 'update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(
        path: '/api/v1/respuestas/{id}',
        summary: 'Actualizar una respuesta',
        tags: ['Respuestas']
    )]
    #[OA\Patch(
        path: '/api/v1/respuestas/{id}',
        summary: 'Actualizar parcialmente una respuesta',
        tags: ['Respuestas']
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID de la respuesta',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'preguntaId', type: 'integer', example: 1),
                new OA\Property(property: 'texto', type: 'string', example: 'París'),
                new OA\Property(property: 'esCorrecta', type: 'boolean', example: true)
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Respuesta actualizada exitosamente',
        content: new OA\JsonContent(ref: Respuesta::class)
    )]
    #[OA\Response(
        response: 404,
        description: 'Respuesta no encontrada'
    )]
    #[OA\Response(
        response: 400,
        description: 'Datos inválidos'
    )]
    public function update(int $id, Request $request): JsonResponse
    {
        $respuesta = $this->respuestaRepository->find($id);

        if (!$respuesta) {
            return $this->json(['error' => 'Respuesta no encontrada'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['preguntaId'])) {
            $pregunta = $this->preguntaRepository->find($data['preguntaId']);
            if (!$pregunta) {
                return $this->json(
                    ['error' => 'Pregunta no encontrada'],
                    Response::HTTP_NOT_FOUND
                );
            }
            $respuesta->setPregunta($pregunta);
        }

        if (isset($data['texto'])) {
            $respuesta->setTexto($data['texto']);
        }

        if (isset($data['esCorrecta'])) {
            if (!is_bool($data['esCorrecta'])) {
                return $this->json(
                    ['error' => 'El campo esCorrecta debe ser un valor booleano'],
                    Response::HTTP_BAD_REQUEST
                );
            }
            $respuesta->setEsCorrecta($data['esCorrecta']);
        }

        $this->entityManager->flush();

        return $this->json($this->serializeRespuesta($respuesta));
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/v1/respuestas/{id}',
        summary: 'Eliminar una respuesta',
        tags: ['Respuestas']
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID de la respuesta',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 204,
        description: 'Respuesta eliminada exitosamente'
    )]
    #[OA\Response(
        response: 404,
        description: 'Respuesta no encontrada'
    )]
    public function delete(int $id): JsonResponse
    {
        $respuesta = $this->respuestaRepository->find($id);

        if (!$respuesta) {
            return $this->json(['error' => 'Respuesta no encontrada'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($respuesta);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    private function serializeRespuesta(Respuesta $respuesta): array
    {
        return [
            'id' => $respuesta->getId(),
            'preguntaId' => $respuesta->getPregunta()?->getId(),
            'texto' => $respuesta->getTexto(),
            'esCorrecta' => $respuesta->getEsCorrecta(),
            'createdAt' => $respuesta->getCreatedAt()?->format('Y-m-d H:i:s'),
        ];
    }
}
