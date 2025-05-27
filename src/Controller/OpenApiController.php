<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OpenApiController
{
    public function serveSpec(): Response
    {
        $filePath = dirname(__DIR__, 2) . '/openapi.yaml';

        if (!file_exists($filePath)) {
            return new Response('OpenAPI file not found.', 404);
        }

        return new BinaryFileResponse($filePath);
    }
}
