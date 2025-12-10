<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class BaseAdminController extends AbstractController
{
    protected function getMenuItems(): array
    {
        return [
            ['label' => 'Dashboard', 'icon' => 'fa-home', 'route' => 'admin'],
            ['label' => 'Usuarios', 'icon' => 'fa-users', 'route' => 'admin_usuario_index'],
            ['label' => 'Trivias', 'icon' => 'fa-question-circle', 'route' => 'admin_trivia_index'],
            ['label' => 'Preguntas', 'icon' => 'fa-question', 'route' => 'admin_pregunta_index'],
        ];
    }

    protected function renderWithMenu(string $view, array $parameters = []): \Symfony\Component\HttpFoundation\Response
    {
        $parameters['menuItems'] = $this->getMenuItems();
        return $this->render($view, $parameters);
    }
}
