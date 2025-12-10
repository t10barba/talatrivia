<?php

namespace App\Controller\Admin;

use App\DTO\Trivia;
use App\Service\TriviaService;
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

        return $this->renderWithMenu('admin/trivia/detail.html.twig', [
            'trivia' => $trivia,
        ]);
    }

    #[Route('/new/form', name: 'admin_trivia_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $trivia = new Trivia();

        if ($request->isMethod('POST')) {
            $trivia->setNombre($request->request->get('nombre'));
            $trivia->setDescripcion($request->request->get('descripcion'));

            try {
                $this->triviaService->create($trivia);
                $this->addFlash('success', 'Trivia creada exitosamente.');
                
                return $this->redirectToRoute('admin_trivia_index');
            } catch (\Exception $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->renderWithMenu('admin/trivia/new.html.twig', [
            'trivia' => $trivia,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_trivia_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        $trivia = $this->triviaService->findById($id);

        if (!$trivia) {
            throw $this->createNotFoundException('Trivia no encontrada');
        }

        if ($request->isMethod('POST')) {
            $trivia->setNombre($request->request->get('nombre'));
            $trivia->setDescripcion($request->request->get('descripcion'));

            try {
                $this->triviaService->update($id, $trivia);
                $this->addFlash('success', 'Trivia actualizada exitosamente.');
                
                return $this->redirectToRoute('admin_trivia_index');
            } catch (\Exception $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->renderWithMenu('admin/trivia/edit.html.twig', [
            'trivia' => $trivia,
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
