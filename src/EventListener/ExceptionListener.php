<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ExceptionListener
{
    #[AsEventListener]
    public function onExceptionEvent(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof HttpException) {
            $data = [
                'status' => $exception->getStatusCode(),
                'message' => $exception->getMessage(),
            ];
            $event->setResponse(new JsonResponse($data));
        } else {
            $data = [
                'status' => 500,
                'message' => 'Une erreur est survenue.',
                'debug' => $exception->getMessage(),
            ];
            $event->setResponse(new JsonResponse($data));
        }
    }
}
