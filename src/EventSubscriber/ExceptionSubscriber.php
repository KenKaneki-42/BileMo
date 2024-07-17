<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\JsonResponse;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if ($exception instanceof HttpException) {
          $statusCode = $exception->getStatusCode();

          $message = $statusCode === 404 ? "la ressource n'existe pas" : $exception->getMessage();
          $data = [
              'status' => $statusCode,
              'message' => $message
          ];
          $response = new JsonResponse($data, $statusCode);
        } else {
          $data = [
              'status' => 500,
              'message' => 'Internal Server Error'
          ];
          $response = new JsonResponse($data, 500);
        }

        $event->setResponse($response);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
