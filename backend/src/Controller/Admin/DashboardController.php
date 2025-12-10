<?php

namespace App\Controller\Admin;

use App\DTO\Usuario;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $menuItems = [
            ['label' => 'Dashboard', 'icon' => 'fa-home', 'route' => 'admin'],
            ['label' => 'Usuarios', 'icon' => 'fa-users', 'route' => 'admin_usuario_index'],
            ['label' => 'Trivias', 'icon' => 'fa-question-circle', 'route' => 'admin_trivia_index'],
        ];
        
        return $this->render('admin/dashboard.html.twig', [
            'menuItems' => $menuItems,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('TalaTrivia - Administración')
            ->setFaviconPath('favicon.ico');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToRoute('Usuarios', 'fa fa-users', 'admin_usuario_index');
        yield MenuItem::linkToRoute('Trivias', 'fa fa-question-circle', 'admin_trivia_index');
    }
}
