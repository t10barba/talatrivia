<?php

namespace App\Controller\Admin;

use App\DTO\Pregunta;
use App\Service\PreguntaService;
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
        private AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    #[Route('', name: 'admin_pregunta_index', methods: ['GET'])]
    public function index(): Response
    {
        $preguntas = $this->preguntaService->findAll();
        
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
                $this->preguntaService->create($pregunta);
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

        if ($request->isMethod('POST')) {
            $pregunta->setTexto($request->request->get('texto'));
            $pregunta->setDificultad((int)$request->request->get('dificultad'));

            try {
                $this->preguntaService->update($id, $pregunta);
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
