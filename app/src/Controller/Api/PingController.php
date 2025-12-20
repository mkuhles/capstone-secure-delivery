<?php
declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class PingController
{
    #[Route('/api/ping', name: 'api_ping', methods: ['POST', 'OPTIONS'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['ok' => true, 'message' => 'pong']);
    }
}
