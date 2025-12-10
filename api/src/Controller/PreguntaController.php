<?php

namespace App\Controller;

use App\Entity\Pregunta;
use App\Repository\PreguntaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/v1/preguntas', name: 'api_preguntas_')]
#[OA\Tag(name: 'Preguntas')]
class PreguntaController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PreguntaRepository $preguntaRepository
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    #[OA\Get(
        path: '/api/v1/preguntas',
        summary: 'Listar todas las preguntas',
        tags: ['Preguntas']
    )]
    #[OA\Response(
        response: 200,
        description: 'Lista de preguntas obtenida exitosamente',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(type: 'object', ref: new OA\Schema(ref: Pregunta::class))
        )
    )]
    public function index(): JsonResponse
    {
        $preguntas = $this->preguntaRepository->findAll();
        
        $data = array_map(function (Pregunta $pregunta) {
            return $this->serializePregunta($pregunta);
        }, $preguntas);

        return $this->json($data);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        path: '/api/v1/preguntas/{id}',
        summary: 'Obtener una pregunta por ID',
        tags: ['Preguntas']
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID de la pregunta',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Pregunta encontrada',
        content: new OA\JsonContent(ref: Pregunta::class)
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
    public function show(int $id): JsonResponse
    {
        $pregunta = $this->preguntaRepository->find($id);

        if (!$pregunta) {
            return $this->json(['error' => 'Pregunta no encontrada'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($this->serializePregunta($pregunta));
    }

    #[Route('', name: 'create', methods: ['POST'])]
    #[OA\Post(
        path: '/api/v1/preguntas',
        summary: 'Crear una nueva pregunta',
        tags: ['Preguntas']
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['texto', 'dificultad'],
            properties: [
                new OA\Property(property: 'texto', type: 'string', example: '¿Cuál es la capital de Francia?'),
                new OA\Property(property: 'dificultad', type: 'integer', example: 1, description: 'Nivel de dificultad (1=fácil, 2=medio, 3=difícil)')
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Pregunta creada exitosamente',
        content: new OA\JsonContent(ref: Pregunta::class)
    )]
    #[OA\Response(
        response: 400,
        description: 'Datos inválidos o incompletos',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'error', type: 'string', example: 'Los campos texto y dificultad son requeridos')
            ]
        )
    )]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['texto']) || !isset($data['dificultad'])) {
            return $this->json(
                ['error' => 'Los campos texto y dificultad son requeridos'],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Validar que la dificultad sea un número válido entre 1 y 3
        if (!is_numeric($data['dificultad']) || $data['dificultad'] < 1 || $data['dificultad'] > 3) {
            return $this->json(
                ['error' => 'La dificultad debe ser un número entre 1 y 3 (1=fácil, 2=medio, 3=difícil)'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $pregunta = new Pregunta();
        $pregunta->setTexto($data['texto']);
        $pregunta->setDificultad((int)$data['dificultad']);

        $this->entityManager->persist($pregunta);
        $this->entityManager->flush();

        return $this->json(
            $this->serializePregunta($pregunta),
            Response::HTTP_CREATED
        );
    }

    #[Route('/{id}', name: 'update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(
        path: '/api/v1/preguntas/{id}',
        summary: 'Actualizar una pregunta',
        tags: ['Preguntas']
    )]
    #[OA\Patch(
        path: '/api/v1/preguntas/{id}',
        summary: 'Actualizar parcialmente una pregunta',
        tags: ['Preguntas']
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID de la pregunta',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'texto', type: 'string', example: '¿Cuál es la capital de Francia?'),
                new OA\Property(property: 'dificultad', type: 'integer', example: 1, description: 'Nivel de dificultad (1=fácil, 2=medio, 3=difícil)')
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Pregunta actualizada exitosamente',
        content: new OA\JsonContent(ref: Pregunta::class)
    )]
    #[OA\Response(
        response: 404,
        description: 'Pregunta no encontrada'
    )]
    #[OA\Response(
        response: 400,
        description: 'Datos inválidos'
    )]
    public function update(int $id, Request $request): JsonResponse
    {
        $pregunta = $this->preguntaRepository->find($id);

        if (!$pregunta) {
            return $this->json(['error' => 'Pregunta no encontrada'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['texto'])) {
            $pregunta->setTexto($data['texto']);
        }

        if (isset($data['dificultad'])) {
            // Validar que la dificultad sea un número válido entre 1 y 3
            if (!is_numeric($data['dificultad']) || $data['dificultad'] < 1 || $data['dificultad'] > 3) {
                return $this->json(
                    ['error' => 'La dificultad debe ser un número entre 1 y 3 (1=fácil, 2=medio, 3=difícil)'],
                    Response::HTTP_BAD_REQUEST
                );
            }
            $pregunta->setDificultad((int)$data['dificultad']);
        }

        $this->entityManager->flush();

        return $this->json($this->serializePregunta($pregunta));
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/v1/preguntas/{id}',
        summary: 'Eliminar una pregunta',
        tags: ['Preguntas']
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID de la pregunta',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 204,
        description: 'Pregunta eliminada exitosamente'
    )]
    #[OA\Response(
        response: 404,
        description: 'Pregunta no encontrada'
    )]
    public function delete(int $id): JsonResponse
    {
        $pregunta = $this->preguntaRepository->find($id);

        if (!$pregunta) {
            return $this->json(['error' => 'Pregunta no encontrada'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($pregunta);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    private function serializePregunta(Pregunta $pregunta): array
    {
        return [
            'id' => $pregunta->getId(),
            'texto' => $pregunta->getTexto(),
            'dificultad' => $pregunta->getDificultad(),
            'createdAt' => $pregunta->getCreatedAt()?->format('Y-m-d H:i:s'),
        ];
    }
}
