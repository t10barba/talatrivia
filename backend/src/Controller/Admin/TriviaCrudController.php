<?php

namespace App\Controller\Admin;

use App\DTO\Trivia;
use App\Service\TriviaService;
use App\Service\TriviaPreguntaService;
use App\Service\PreguntaService;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/trivia')]
class TriviaCrudController extends BaseAdminController
{
    public function __construct(
        private TriviaService $triviaService,
        private TriviaPreguntaService $triviaPreguntaService,
        private PreguntaService $preguntaService,
        private AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    #[Route('', name: 'admin_trivia_index', methods: ['GET'])]
    public function index(): Response
    {
        $trivias = $this->triviaService->findAll();
        
        return $this->renderWithMenu('admin/trivia/index.html.twig', [
            'trivias' => $trivias,
        ]);
    }

    #[Route('/{id}', name: 'admin_trivia_detail', methods: ['GET'])]
    public function detail(int $id): Response
    {
        $trivia = $this->triviaService->findById($id);

        if (!$trivia) {
            throw $this->createNotFoundException('Trivia no encontrada');
        }

        // Cargar preguntas de la trivia
        $preguntasTrivia = $this->triviaPreguntaService->findByTriviaId($id);
        
        // Obtener los detalles completos de cada pregunta
        $preguntas = [];
        foreach ($preguntasTrivia as $pt) {
            $pregunta = $this->preguntaService->findById($pt['preguntaId']);
            if ($pregunta) {
                $pregunta->orden = $pt['orden'];
                $preguntas[] = $pregunta;
            }
        }

        return $this->renderWithMenu('admin/trivia/detail.html.twig', [
            'trivia' => $trivia,
            'preguntas' => $preguntas,
        ]);
    }

    #[Route('/new/form', name: 'admin_trivia_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $trivia = new Trivia();

        if ($request->isMethod('POST')) {
            $trivia->setSlug($request->request->get('slug'));
            $trivia->setNombre($request->request->get('nombre'));
            $trivia->setDescripcion($request->request->get('descripcion'));

            try {
                $triviaCreada = $this->triviaService->create($trivia);
                
                // Asignar preguntas seleccionadas
                $preguntasSeleccionadas = $request->request->all('preguntas') ?? [];
                if (!empty($preguntasSeleccionadas)) {
                    $preguntas = [];
                    foreach ($preguntasSeleccionadas as $index => $preguntaId) {
                        $preguntas[] = [
                            'preguntaId' => (int)$preguntaId,
                            'orden' => $index + 1
                        ];
                    }
                    $this->triviaPreguntaService->bulkAssign($triviaCreada->getId(), $preguntas);
                }
                
                $this->addFlash('success', 'Trivia creada exitosamente.');
                
                return $this->redirectToRoute('admin_trivia_index');
            } catch (\Exception $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        // Cargar todas las preguntas disponibles
        $todasLasPreguntas = $this->preguntaService->findAll();

        return $this->renderWithMenu('admin/trivia/new.html.twig', [
            'trivia' => $trivia,
            'todasLasPreguntas' => $todasLasPreguntas,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_trivia_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        $trivia = $this->triviaService->findById($id);

        if (!$trivia) {
            throw $this->createNotFoundException('Trivia no encontrada');
        }

        // Cargar preguntas actuales de la trivia
        $preguntasActuales = $this->triviaPreguntaService->findByTriviaId($id);
        $preguntasIds = array_column($preguntasActuales, 'preguntaId');

        if ($request->isMethod('POST')) {
            $trivia->setSlug($request->request->get('slug'));
            $trivia->setNombre($request->request->get('nombre'));
            $trivia->setDescripcion($request->request->get('descripcion'));

            try {
                $this->triviaService->update($id, $trivia);
                
                // Actualizar preguntas seleccionadas
                $preguntasSeleccionadas = $request->request->all('preguntas') ?? [];
                $preguntas = [];
                foreach ($preguntasSeleccionadas as $index => $preguntaId) {
                    $preguntas[] = [
                        'preguntaId' => (int)$preguntaId,
                        'orden' => $index + 1
                    ];
                }
                
                try {
                    $this->triviaPreguntaService->bulkAssign($id, $preguntas);
                } catch (\Exception $bulkError) {
                    // Log más detallado del error de bulk assign
                    throw new \Exception('Error al asignar preguntas: ' . $bulkError->getMessage() . ' | Tipo: ' . get_class($bulkError));
                }
                
                $this->addFlash('success', 'Trivia actualizada exitosamente.');
                
                return $this->redirectToRoute('admin_trivia_index');
            } catch (\Exception $e) {
                $errorMessage = $e->getMessage() ?: 'Error desconocido: ' . get_class($e);
                $this->addFlash('danger', $errorMessage);
            }
        }

        // Cargar todas las preguntas disponibles
        $todasLasPreguntas = $this->preguntaService->findAll();

        return $this->renderWithMenu('admin/trivia/edit.html.twig', [
            'trivia' => $trivia,
            'todasLasPreguntas' => $todasLasPreguntas,
            'preguntasSeleccionadas' => $preguntasIds,
            'preguntasActuales' => $preguntasActuales,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_trivia_delete', methods: ['GET'])]
    public function delete(int $id): RedirectResponse
    {
        try {
            $this->triviaService->delete($id);
            $this->addFlash('success', 'Trivia eliminada exitosamente.');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Error al eliminar la trivia: ' . $e->getMessage());
        }

        return $this->redirectToRoute('admin_trivia_index');
    }
}
