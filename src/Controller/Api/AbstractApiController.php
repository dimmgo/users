<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractApiController extends AbstractController
{
    use MicroKernelTrait;

    protected function jsonResponse(mixed $data = [], int $status = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse($data, $status);
    }
}
