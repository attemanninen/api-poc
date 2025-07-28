<?php

namespace App\EventSubscriber;

use App\Exception\AdditionalDetailsExceptionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => [
                // Priority needs to be lower than Symfony ExceptionListener's
                // priority to it being able to log errors.
                ['setResponse', -10],
            ],
        ];
    }

    /**
     * Set response.
     */
    public function setResponse(ExceptionEvent $event)
    {
        if (!$event->isMasterRequest()) {
            // don't do anything if it's not the master request
            return;
        }

        $exception = $event->getThrowable();
        $originalException = $exception;

        if (!$exception instanceof HttpException) {
            return;

            $exception = new HttpException(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR]
            );
        }

        $payload['message'] = $exception->getMessage();

        if ($exception instanceof AdditionalDetailsExceptionInterface) {
            if ($description = $exception->getDescription()) {
                $payload['description'] = $description;
            }

            if ($errors = $exception->getErrors()) {
                $payload['errors'] = $errors;
            }
        }

        $response = new JsonResponse($payload, $exception->getStatusCode());
        $event->setResponse($response);
    }
}
