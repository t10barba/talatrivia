<?php

namespace App\Controller;

use App\Entity\PreguntaTrivia;
use App\Entity\RespuestaUsuario;
use App\Repository\PreguntaTriviaRepository;
use App\Repository\RespuestaRepository;
use App\Repository\RespuestaUsuarioRepository;
use App\Repository\TriviaRepository;
use App\Repository\UsuarioTriviaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/v1/juego')]
#[OA\Tag(name: 'Juego')]
class JuegoController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private LoggerInterface $logger,
        private TriviaRepository $triviaRepo,
        private UsuarioTriviaRepository $usuarioTriviaRepo,
        private PreguntaTriviaRepository $preguntaTriviaRepo,
        private RespuestaRepository $respuestaRepo,
        private RespuestaUsuarioRepository $respuestaUsuarioRepo
    ) {
    }

    /**
     * Método privado reutilizable para validar slug de trivia y código de usuario
     */
    private function validateTriviaAndCodigo(string $slug, string $codigo): array
    {
        $trivia = $this->triviaRepo->findOneBy(['slug' => $slug]);
        
        if (!$trivia) {
            return ['error' => new JsonResponse([
                'error' => 'No encontramos el juego de trivia que buscas :(',
                'code' => 'TRIVIA_NOT_FOUND'
            ], 404)];
        }

        $usuarioTrivia = $this->usuarioTriviaRepo->findOneBy(['codigo' => $codigo]);
        
        if (!$usuarioTrivia || $usuarioTrivia->getTrivia()->getId() !== $trivia->getId()) {
            return ['error' => new JsonResponse([
                'error' => 'El código no es válido para la trivia ' . $trivia->getNombre(),
                'code' => 'INVALID_CODE'
            ], 400)];
        }

        return [
            'trivia' => $trivia,
            'usuarioTrivia' => $usuarioTrivia
        ];
    }

    #[Route('/{slug}/{codigo}/pregunta', name: 'juego_pregunta', methods: ['GET'], requirements: ['slug' => '[a-z0-9\-]+', 'codigo' => '[A-Z0-9]{6}'])]
    #[OA\Get(
        path: '/api/v1/juego/{slug}/{codigo}/pregunta',
        summary: 'Obtener siguiente pregunta sin contestar',
        description: 'Retorna la siguiente pregunta de la trivia que el usuario aún no ha respondido. Actualiza estado a iniciado en primera pregunta.',
        tags: ['Juego']
    )]
    #[OA\Parameter(
        name: 'slug',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string'),
        example: 'rrhh-2025'
    )]
    #[OA\Parameter(
        name: 'codigo',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string', pattern: '[A-Z0-9]{6}'),
        example: 'TEST01'
    )]
    #[OA\Response(
        response: 200,
        description: 'Pregunta obtenida exitosamente',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'preguntaId', type: 'integer', example: 1),
                new OA\Property(property: 'texto', type: 'string', example: '¿Qué es el onboarding en recursos humanos?'),
                new OA\Property(
                    property: 'respuestas',
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'texto', type: 'string', example: 'Es una técnica de capacitación externa')
                        ]
                    )
                ),
                new OA\Property(property: 'numeroPregunta', type: 'integer', example: 1),
                new OA\Property(property: 'totalPreguntas', type: 'integer', example: 10),
                new OA\Property(property: 'nombreTrivia', type: 'string', example: 'Trivia de Recursos Humanos'),
                new OA\Property(property: 'nombreUsuario', type: 'string', example: 'Daniela')
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Trivia no encontrada',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'error', type: 'string'),
                new OA\Property(property: 'code', type: 'string', example: 'TRIVIA_NOT_FOUND')
            ]
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Código inválido',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'error', type: 'string'),
                new OA\Property(property: 'code', type: 'string', example: 'INVALID_CODE')
            ]
        )
    )]
    public function getPregunta(string $slug, string $codigo): JsonResponse
    {
        $validated = $this->validateTriviaAndCodigo($slug, $codigo);
        
        if (isset($validated['error'])) {
            return $validated['error'];
        }

        $trivia = $validated['trivia'];
        $usuarioTrivia = $validated['usuarioTrivia'];

        // Si la trivia ya fue finalizada, retornar inmediatamente
        if ($usuarioTrivia->getEstado() === 2) {
            return new JsonResponse([
                'finalizado' => true,
                'nombreUsuario' => $usuarioTrivia->getUsuario()->getNombre(),
                'nombreTrivia' => $trivia->getNombre()
            ]);
        }

        // Buscar la siguiente pregunta sin contestar
        $query = $this->em->createQuery(
            'SELECT pt FROM App\Entity\PreguntaTrivia pt
             WHERE pt.trivia = :trivia
             AND pt.pregunta NOT IN (
                 SELECT IDENTITY(ru.pregunta)
                 FROM App\Entity\RespuestaUsuario ru
                 WHERE ru.usuarioTrivia = :ut
             )
             ORDER BY pt.orden ASC'
        )
        ->setParameter('trivia', $trivia)
        ->setParameter('ut', $usuarioTrivia)
        ->setMaxResults(1);

        $preguntaTrivia = $query->getOneOrNullResult();

        // Si no hay más preguntas, auto-finalizar la trivia
        if (!$preguntaTrivia) {
            $querySum = $this->em->createQuery(
                'SELECT SUM(ru.puntajeObtenido)
                 FROM App\Entity\RespuestaUsuario ru
                 WHERE ru.usuarioTrivia = :ut'
            )
            ->setParameter('ut', $usuarioTrivia);
            
            $puntajeTotal = $querySum->getSingleScalarResult() ?? 0;
            
            $usuarioTrivia->setEstado(2);
            $usuarioTrivia->setFinishedAt(new \DateTimeImmutable());
            $usuarioTrivia->setPuntajeTotal($puntajeTotal);
            
            $this->em->flush();
            
            $this->logger->warning('Trivia auto-finalizada sin preguntas pendientes', [
                'codigo' => $codigo,
                'puntajeTotal' => $puntajeTotal
            ]);

            return new JsonResponse([
                'finalizado' => true,
                'nombreUsuario' => $usuarioTrivia->getUsuario()->getNombre(),
                'nombreTrivia' => $trivia->getNombre()
            ]);
        }

        $pregunta = $preguntaTrivia->getPregunta();

        // Obtener respuestas de la pregunta
        $respuestas = $this->respuestaRepo->findBy(['pregunta' => $pregunta]);
        
        // Mapear respuestas sin incluir el campo esCorrecta
        $respuestasArray = array_map(function ($r) {
            return [
                'id' => $r->getId(),
                'texto' => $r->getTexto()
            ];
        }, $respuestas);

        // Randomizar el orden de las respuestas
        shuffle($respuestasArray);

        // Verificar si es la primera pregunta (primer acceso)
        $countRespuestas = count($this->respuestaUsuarioRepo->findBy(['usuarioTrivia' => $usuarioTrivia]));
        
        if ($countRespuestas === 0) {
            $usuarioTrivia->setEstado(1);
            $usuarioTrivia->setStartedAt(new \DateTimeImmutable());
            $this->em->flush();
            
            $this->logger->info('Usuario inició trivia', [
                'codigo' => $codigo,
                'slug' => $slug
            ]);
        }

        // Calcular progreso
        $totalPreguntas = count($this->preguntaTriviaRepo->findBy(['trivia' => $trivia]));
        $numeroPregunta = $countRespuestas + 1;

        return new JsonResponse([
            'preguntaId' => $pregunta->getId(),
            'texto' => $pregunta->getTexto(),
            'respuestas' => $respuestasArray,
            'numeroPregunta' => $numeroPregunta,
            'totalPreguntas' => $totalPreguntas,
            'nombreTrivia' => $trivia->getNombre(),
            'nombreUsuario' => $usuarioTrivia->getUsuario()->getNombre()
        ]);
    }

    #[Route('/{slug}/{codigo}/respuesta', name: 'juego_respuesta', methods: ['POST'], requirements: ['slug' => '[a-z0-9\-]+', 'codigo' => '[A-Z0-9]{6}'])]
    #[OA\Post(
        path: '/api/v1/juego/{slug}/{codigo}/respuesta',
        summary: 'Enviar respuesta a pregunta',
        description: 'Valida y registra la respuesta del usuario. Calcula puntaje según dificultad. Finaliza trivia si es última pregunta.',
        tags: ['Juego']
    )]
    #[OA\Parameter(
        name: 'slug',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string'),
        example: 'rrhh-2025'
    )]
    #[OA\Parameter(
        name: 'codigo',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string', pattern: '[A-Z0-9]{6}'),
        example: 'TEST01'
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['respuestaId'],
            properties: [
                new OA\Property(property: 'respuestaId', type: 'integer', example: 1)
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Respuesta procesada exitosamente',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'correcto', type: 'boolean', example: true),
                new OA\Property(property: 'puntajeObtenido', type: 'integer', example: 1),
                new OA\Property(property: 'finalizado', type: 'boolean', example: false)
            ]
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Datos inválidos o trivia ya finalizada',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'error', type: 'string'),
                new OA\Property(property: 'code', type: 'string')
            ]
        )
    )]
    #[OA\Response(
        response: 409,
        description: 'Pregunta ya respondida',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'error', type: 'string', example: 'Ya respondiste esta pregunta'),
                new OA\Property(property: 'code', type: 'string', example: 'ALREADY_ANSWERED')
            ]
        )
    )]
    public function enviarRespuesta(Request $request, string $slug, string $codigo): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['respuestaId'])) {
            return new JsonResponse([
                'error' => 'respuestaId es requerido',
                'code' => 'MISSING_RESPUESTA_ID'
            ], 400);
        }

        $validated = $this->validateTriviaAndCodigo($slug, $codigo);
        
        if (isset($validated['error'])) {
            return $validated['error'];
        }

        $trivia = $validated['trivia'];
        $usuarioTrivia = $validated['usuarioTrivia'];

        // Validar que la trivia no esté ya finalizada
        if ($usuarioTrivia->getEstado() === 2) {
            return new JsonResponse([
                'error' => 'Esta trivia ya ha sido completada',
                'code' => 'TRIVIA_ALREADY_FINISHED'
            ], 400);
        }

        try {
            $this->em->beginTransaction();

            // Buscar la pregunta esperada (siguiente sin contestar)
            $query = $this->em->createQuery(
                'SELECT pt FROM App\Entity\PreguntaTrivia pt
                 WHERE pt.trivia = :trivia
                 AND pt.pregunta NOT IN (
                     SELECT IDENTITY(ru.pregunta)
                     FROM App\Entity\RespuestaUsuario ru
                     WHERE ru.usuarioTrivia = :ut
                 )
                 ORDER BY pt.orden ASC'
            )
            ->setParameter('trivia', $trivia)
            ->setParameter('ut', $usuarioTrivia)
            ->setMaxResults(1);

            $preguntaTrivia = $query->getOneOrNullResult();

            if (!$preguntaTrivia) {
                return new JsonResponse([
                    'error' => 'No hay preguntas pendientes',
                    'code' => 'NO_PENDING_QUESTIONS'
                ], 400);
            }

            $pregunta = $preguntaTrivia->getPregunta();

            // Verificar que no exista ya una respuesta para esta pregunta (evitar duplicados)
            $existente = $this->respuestaUsuarioRepo->findOneBy([
                'usuarioTrivia' => $usuarioTrivia,
                'pregunta' => $pregunta
            ]);

            if ($existente) {
                return new JsonResponse([
                    'error' => 'Ya respondiste esta pregunta',
                    'code' => 'ALREADY_ANSWERED'
                ], 409);
            }

            // Obtener la respuesta enviada
            $respuesta = $this->respuestaRepo->find($data['respuestaId']);
            
            if (!$respuesta) {
                return new JsonResponse([
                    'error' => 'Respuesta no encontrada',
                    'code' => 'RESPUESTA_NOT_FOUND'
                ], 404);
            }

            // Validar que la respuesta pertenece a la pregunta actual
            if ($respuesta->getPregunta()->getId() !== $pregunta->getId()) {
                return new JsonResponse([
                    'error' => 'La respuesta no corresponde a la pregunta actual',
                    'code' => 'INVALID_ANSWER_FOR_QUESTION'
                ], 400);
            }

            // Crear el registro de respuesta del usuario
            $esCorrecta = $respuesta->getEsCorrecta();
            $puntaje = $esCorrecta ? $pregunta->getDificultad() : 0;

            $respuestaUsuario = new RespuestaUsuario();
            $respuestaUsuario->setUsuarioTrivia($usuarioTrivia);
            $respuestaUsuario->setPregunta($pregunta);
            $respuestaUsuario->setRespuesta($respuesta);
            $respuestaUsuario->setEsCorrecta($esCorrecta);
            $respuestaUsuario->setPuntajeObtenido($puntaje);

            $this->em->persist($respuestaUsuario);
            $this->em->flush();

            $this->logger->info('Respuesta enviada', [
                'codigo' => $codigo,
                'correcta' => $esCorrecta,
                'puntaje' => $puntaje
            ]);

            // Verificar si es la última pregunta
            $totalRespondidas = count($this->respuestaUsuarioRepo->findBy(['usuarioTrivia' => $usuarioTrivia]));
            $totalPreguntas = count($this->preguntaTriviaRepo->findBy(['trivia' => $trivia]));
            
            $finalizado = false;
            
            if ($totalRespondidas >= $totalPreguntas) {
                // Calcular puntaje total
                $querySum = $this->em->createQuery(
                    'SELECT SUM(ru.puntajeObtenido)
                     FROM App\Entity\RespuestaUsuario ru
                     WHERE ru.usuarioTrivia = :ut'
                )
                ->setParameter('ut', $usuarioTrivia);
                
                $puntajeTotal = $querySum->getSingleScalarResult() ?? 0;
                
                $usuarioTrivia->setEstado(2);
                $usuarioTrivia->setFinishedAt(new \DateTimeImmutable());
                $usuarioTrivia->setPuntajeTotal($puntajeTotal);
                
                $this->em->flush();
                
                $finalizado = true;
                
                $this->logger->info('Trivia finalizada', [
                    'codigo' => $codigo,
                    'puntajeTotal' => $puntajeTotal
                ]);
            }

            $this->em->commit();

            return new JsonResponse([
                'correcto' => $esCorrecta,
                'puntajeObtenido' => $puntaje,
                'finalizado' => $finalizado
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Error al enviar respuesta', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    #[Route('/{slug}/{codigo}/puntaje', name: 'juego_puntaje', methods: ['GET'], requirements: ['slug' => '[a-z0-9\-]+', 'codigo' => '[A-Z0-9]{6}'])]
    #[OA\Get(
        path: '/api/v1/juego/{slug}/{codigo}/puntaje',
        summary: 'Obtener puntaje final',
        description: 'Retorna el puntaje total y tiempo transcurrido de la trivia completada.',
        tags: ['Juego']
    )]
    #[OA\Parameter(
        name: 'slug',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string'),
        example: 'rrhh-2025'
    )]
    #[OA\Parameter(
        name: 'codigo',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string', pattern: '[A-Z0-9]{6}'),
        example: 'TEST01'
    )]
    #[OA\Response(
        response: 200,
        description: 'Puntaje obtenido',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'puntajeTotal', type: 'integer', example: 18),
                new OA\Property(property: 'tiempoTranscurrido', type: 'string', example: '05:32'),
                new OA\Property(property: 'nombreTrivia', type: 'string', example: 'Trivia de Recursos Humanos'),
                new OA\Property(property: 'nombreUsuario', type: 'string', example: 'Daniela')
            ]
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Trivia no finalizada o datos incompletos',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'error', type: 'string'),
                new OA\Property(property: 'code', type: 'string')
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Trivia no encontrada'
    )]
    public function getPuntaje(string $slug, string $codigo): JsonResponse
    {
        $validated = $this->validateTriviaAndCodigo($slug, $codigo);
        
        if (isset($validated['error'])) {
            return $validated['error'];
        }

        $trivia = $validated['trivia'];
        $usuarioTrivia = $validated['usuarioTrivia'];

        // Validar que la trivia esté finalizada
        if ($usuarioTrivia->getEstado() !== 2) {
            return new JsonResponse([
                'error' => 'La trivia aún no ha sido finalizada',
                'code' => 'TRIVIA_NOT_FINISHED'
            ], 400);
        }

        // Validar que existan los timestamps
        if (!$usuarioTrivia->getStartedAt() || !$usuarioTrivia->getFinishedAt()) {
            return new JsonResponse([
                'error' => 'Datos de trivia incompletos',
                'code' => 'INVALID_GAME_STATE'
            ], 400);
        }

        // Calcular tiempo transcurrido
        $diff = $usuarioTrivia->getFinishedAt()->getTimestamp() - $usuarioTrivia->getStartedAt()->getTimestamp();
        $minutos = floor($diff / 60);
        $segundos = $diff % 60;
        $tiempoFormateado = sprintf("%02d:%02d", $minutos, $segundos);

        return new JsonResponse([
            'puntajeTotal' => $usuarioTrivia->getPuntajeTotal(),
            'tiempoTranscurrido' => $tiempoFormateado,
            'nombreTrivia' => $trivia->getNombre(),
            'nombreUsuario' => $usuarioTrivia->getUsuario()->getNombre()
        ]);
    }

    #[Route('/{slug}/ranking', name: 'juego_ranking', methods: ['GET'], requirements: ['slug' => '[a-z0-9\-]+'])]
    #[OA\Get(
        path: '/api/v1/juego/{slug}/ranking',
        summary: 'Obtener ranking de mejores puntajes',
        description: 'Retorna el top de usuarios con mejores puntajes para una trivia específica. Solo incluye trivias finalizadas (estado=2). Ordenado por puntaje descendente y tiempo ascendente.',
        tags: ['Juego']
    )]
    #[OA\Parameter(
        name: 'slug',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string'),
        example: 'rrhh-2025'
    )]
    #[OA\Parameter(
        name: 'limit',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'integer', default: 10),
        description: 'Número máximo de resultados a retornar'
    )]
    #[OA\Response(
        response: 200,
        description: 'Lista de mejores puntajes',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'nombreTrivia', type: 'string', example: 'Trivia RRHH 2025'),
                new OA\Property(
                    property: 'ranking',
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'posicion', type: 'integer', example: 1),
                            new OA\Property(property: 'nombreUsuario', type: 'string', example: 'Juan Pérez'),
                            new OA\Property(property: 'puntajeTotal', type: 'integer', example: 18),
                            new OA\Property(property: 'tiempoTranscurrido', type: 'string', example: '03:45'),
                            new OA\Property(property: 'finishedAt', type: 'string', format: 'date-time', example: '2025-12-11T15:30:00+00:00')
                        ]
                    )
                )
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Trivia no encontrada',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'error', type: 'string', example: 'No encontramos el juego de trivia que buscas :('),
                new OA\Property(property: 'code', type: 'string', example: 'TRIVIA_NOT_FOUND')
            ]
        )
    )]
    public function getRanking(string $slug, Request $request): JsonResponse
    {
        $trivia = $this->triviaRepo->findOneBy(['slug' => $slug]);
        
        if (!$trivia) {
            return new JsonResponse([
                'error' => 'No encontramos el juego de trivia que buscas :(',
                'code' => 'TRIVIA_NOT_FOUND'
            ], 404);
        }

        $limit = (int) $request->query->get('limit', 10);
        if ($limit < 1 || $limit > 100) {
            $limit = 10;
        }

        // Obtener usuarios que completaron la trivia, ordenados por puntaje DESC y tiempo ASC
        $qb = $this->em->createQueryBuilder();
        $qb->select('ut', 'u')
            ->from('App\Entity\UsuarioTrivia', 'ut')
            ->join('ut.usuario', 'u')
            ->where('ut.trivia = :trivia')
            ->andWhere('ut.estado = 2')
            ->andWhere('ut.startedAt IS NOT NULL')
            ->andWhere('ut.finishedAt IS NOT NULL')
            ->setParameter('trivia', $trivia)
            ->orderBy('ut.puntajeTotal', 'DESC')
            ->addOrderBy('ut.finishedAt - ut.startedAt', 'ASC') // Menor tiempo primero
            ->setMaxResults($limit);

        $usuariosTrivias = $qb->getQuery()->getResult();

        $ranking = [];
        $posicion = 1;

        foreach ($usuariosTrivias as $ut) {
            $diff = $ut->getFinishedAt()->getTimestamp() - $ut->getStartedAt()->getTimestamp();
            $minutos = floor($diff / 60);
            $segundos = $diff % 60;
            $tiempoFormateado = sprintf("%02d:%02d", $minutos, $segundos);

            $ranking[] = [
                'posicion' => $posicion++,
                'nombreUsuario' => $ut->getUsuario()->getNombre(),
                'puntajeTotal' => $ut->getPuntajeTotal(),
                'tiempoTranscurrido' => $tiempoFormateado,
                'finishedAt' => $ut->getFinishedAt()->format('c')
            ];
        }

        return new JsonResponse([
            'nombreTrivia' => $trivia->getNombre(),
            'ranking' => $ranking
        ]);
    }
}
