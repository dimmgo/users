<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public const ENV_LOCAL = 'local';
    public const ENV_DEV = 'dev';
    public const ENV_PROD = 'prod';

    public function __construct(private readonly string $env)
    {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $statusCode = 500;
        $message = 'Internal Server Error';
        $extra = [];

        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
            $message = $exception->getMessage();
        } elseif ($exception instanceof AuthenticationException) {
            $statusCode = 401;
            $message = 'Authentication required';
        } elseif ($exception instanceof AccessDeniedException) {
            $statusCode = 403;
            $message = 'Access denied';
        } else {
            if ($this->env === self::ENV_DEV || $this->env === self::ENV_LOCAL) {
                $message = $exception->getMessage();
            }
        }

        if ($this->env === self::ENV_DEV || $this->env === self::ENV_LOCAL) {
            $extra = [
                'exception_class' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTrace(),
            ];
        }

        $response = new JsonResponse([
            'success' => false,
            'error' => array_merge([
                'code' => $statusCode,
                'message' => $message,
            ], $extra)
        ], $statusCode);

        $event->setResponse($response);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
