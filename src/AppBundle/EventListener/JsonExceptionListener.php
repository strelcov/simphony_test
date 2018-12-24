<?php
namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;


class JsonExceptionListener
{
    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $request = $event->getRequest();
        $routeName = $request->get('_route');
        if (stripos($routeName, 'api_book_') !== false) {
            $exception = $event->getException();
            $customResponse = new JsonResponse(['error' => ['message' => $exception->getMessage()]], 500);
            $event->setResponse($customResponse);
        }
    }
}