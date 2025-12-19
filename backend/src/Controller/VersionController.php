<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class VersionController extends AbstractController
{
    #[Route('/api/version', name: 'api_version', methods: ['GET'])]
    public function getVersion(): JsonResponse
    {
        $version = $_ENV['APP_VERSION'] ?? 'unknown';
        
        return $this->json([
            'version' => $version,
            'application' => 'TalaTrivia Backend',
            'timestamp' => time()
        ]);
    }
}
