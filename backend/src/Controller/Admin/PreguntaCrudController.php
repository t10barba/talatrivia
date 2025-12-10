<?php

namespace App\Controller\Admin;

use App\DTO\Pregunta;
use App\DTO\Respuesta;
use App\Service\PreguntaService;
use App\Service\RespuestaService;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/pregunta')]
class PreguntaCrudController extends BaseAdminController
{
    public function __construct(
        private PreguntaService $preguntaService,
        private RespuestaService $respuestaService,
        private AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    #[Route('', name: 'admin_pregunta_index', methods: ['GET'])]
    public function index(): Response
    {
        $preguntas = $this->preguntaService->findAll();
        
        // Cargar el conteo de respuestas para cada pregunta
        foreach ($preguntas as $pregunta) {
            $respuestas = $this->respuestaService->findByPreguntaId($pregunta->getId());
            $pregunta->setRespuestas($respuestas);
        }
        
        return $this->renderWithMenu('admin/pregunta/index.html.twig', [
            'preguntas' => $preguntas,
        ]);
    }

    #[Route('/{id}', name: 'admin_pregunta_detail', methods: ['GET'])]
    public function detail(int $id): Response
    {
        $pregunta = $this->preguntaService->findById($id);

        if (!$pregunta) {
            throw $this->createNotFoundException('Pregunta no encontrada');
        }

        // Cargar respuestas
        $respuestas = $this->respuestaService->findByPreguntaId($id);
        $pregunta->setRespuestas($respuestas);

        return $this->renderWithMenu('admin/pregunta/detail.html.twig', [
            'pregunta' => $pregunta,
        ]);
    }

    #[Route('/new/form', name: 'admin_pregunta_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $pregunta = new Pregunta();

        if ($request->isMethod('POST')) {
            $pregunta->setTexto($request->request->get('texto'));
            $pregunta->setDificultad((int)$request->request->get('dificultad'));

            try {
                // Crear la pregunta
                $preguntaCreada = $this->preguntaService->create($pregunta);
                
                // Crear las respuestas
                $respuestas = $request->request->all('respuestas') ?? [];
                $respuestaCorrecta = $request->request->get('respuesta_correcta');
                
                foreach ($respuestas as $index => $textoRespuesta) {
                    if (!empty($textoRespuesta)) {
                        $respuesta = new Respuesta();
                        $respuesta->setPreguntaId($preguntaCreada->getId());
                        $respuesta->setTexto($textoRespuesta);
                        $respuesta->setEsCorrecta($respuestaCorrecta == $index);
                        $this->respuestaService->create($respuesta);
                    }
                }
                
                $this->addFlash('success', 'Pregunta creada exitosamente.');
                
                return $this->redirectToRoute('admin_pregunta_index');
            } catch (\Exception $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->renderWithMenu('admin/pregunta/new.html.twig', [
            'pregunta' => $pregunta,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_pregunta_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        $pregunta = $this->preguntaService->findById($id);

        if (!$pregunta) {
            throw $this->createNotFoundException('Pregunta no encontrada');
        }

        // Cargar respuestas existentes
        $respuestas = $this->respuestaService->findByPreguntaId($id);
        $pregunta->setRespuestas($respuestas);

        if ($request->isMethod('POST')) {
            $pregunta->setTexto($request->request->get('texto'));
            $pregunta->setDificultad((int)$request->request->get('dificultad'));

            try {
                // Actualizar la pregunta
                $this->preguntaService->update($id, $pregunta);
                
                // Procesar respuestas
                $respuestasTexto = $request->request->all('respuestas') ?? [];
                $respuestasIds = $request->request->all('respuestas_ids') ?? [];
                $respuestasEliminar = $request->request->all('respuestas_eliminar') ?? [];
                $respuestaCorrecta = $request->request->get('respuesta_correcta');
                
                // Eliminar respuestas marcadas
                foreach ($respuestasEliminar as $respuestaId) {
                    if (!empty($respuestaId)) {
                        $this->respuestaService->delete((int)$respuestaId);
                    }
                }
                
                // Actualizar y crear respuestas
                foreach ($respuestasTexto as $index => $textoRespuesta) {
                    if (!empty($textoRespuesta)) {
                        $respuestaId = $respuestasIds[$index] ?? null;
                        $esCorrecta = $respuestaCorrecta == $index;
                        
                        if (!empty($respuestaId)) {
                            // Actualizar respuesta existente
                            $respuesta = new Respuesta();
                            $respuesta->setPreguntaId($id);
                            $respuesta->setTexto($textoRespuesta);
                            $respuesta->setEsCorrecta($esCorrecta);
                            $this->respuestaService->update((int)$respuestaId, $respuesta);
                        } else {
                            // Crear nueva respuesta
                            $respuesta = new Respuesta();
                            $respuesta->setPreguntaId($id);
                            $respuesta->setTexto($textoRespuesta);
                            $respuesta->setEsCorrecta($esCorrecta);
                            $this->respuestaService->create($respuesta);
                        }
                    }
                }
                
                $this->addFlash('success', 'Pregunta actualizada exitosamente.');
                
                return $this->redirectToRoute('admin_pregunta_index');
            } catch (\Exception $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->renderWithMenu('admin/pregunta/edit.html.twig', [
            'pregunta' => $pregunta,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_pregunta_delete', methods: ['GET'])]
    public function delete(int $id): RedirectResponse
    {
        try {
            $this->preguntaService->delete($id);
            $this->addFlash('success', 'Pregunta eliminada exitosamente.');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Error al eliminar la pregunta: ' . $e->getMessage());
        }

        return $this->redirectToRoute('admin_pregunta_index');
    }
}
