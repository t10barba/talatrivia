<?php

namespace App\Controller\Admin;

use App\DTO\Usuario;
use App\Service\UsuarioService;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/usuario')]
class UsuarioCrudController extends AbstractController
{
    public function __construct(
        private UsuarioService $usuarioService,
        private AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    #[Route('', name: 'admin_usuario_index', methods: ['GET'])]
    public function index(): Response
    {
        $usuarios = $this->usuarioService->findAll();
        
        return $this->render('admin/usuario/index.html.twig', [
            'usuarios' => $usuarios,
        ]);
    }

    #[Route('/{id}', name: 'admin_usuario_detail', methods: ['GET'])]
    public function detail(int $id): Response
    {
        $usuario = $this->usuarioService->findById($id);

        if (!$usuario) {
            throw $this->createNotFoundException('Usuario no encontrado');
        }

        return $this->render('admin/usuario/detail.html.twig', [
            'usuario' => $usuario,
        ]);
    }

    #[Route('/new/form', name: 'admin_usuario_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $usuario = new Usuario();

        if ($request->isMethod('POST')) {
            $usuario->setNombre($request->request->get('nombre'));
            $usuario->setApellido($request->request->get('apellido'));
            $usuario->setEmail($request->request->get('email'));

            try {
                $this->usuarioService->create($usuario);
                $this->addFlash('success', 'Usuario creado exitosamente.');
                
                return $this->redirectToRoute('admin_usuario_index');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error al crear el usuario: ' . $e->getMessage());
            }
        }

        return $this->render('admin/usuario/new.html.twig', [
            'usuario' => $usuario,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_usuario_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        $usuario = $this->usuarioService->findById($id);

        if (!$usuario) {
            throw $this->createNotFoundException('Usuario no encontrado');
        }

        if ($request->isMethod('POST')) {
            $usuario->setNombre($request->request->get('nombre'));
            $usuario->setApellido($request->request->get('apellido'));
            $usuario->setEmail($request->request->get('email'));

            try {
                $this->usuarioService->update($id, $usuario);
                $this->addFlash('success', 'Usuario actualizado exitosamente.');
                
                return $this->redirectToRoute('admin_usuario_index');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error al actualizar el usuario: ' . $e->getMessage());
            }
        }

        return $this->render('admin/usuario/edit.html.twig', [
            'usuario' => $usuario,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_usuario_delete', methods: ['GET'])]
    public function delete(int $id): RedirectResponse
    {
        try {
            $this->usuarioService->delete($id);
            $this->addFlash('success', 'Usuario eliminado exitosamente.');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Error al eliminar el usuario: ' . $e->getMessage());
        }

        return $this->redirectToRoute('admin_usuario_index');
    }
}
