<?php

namespace Simplex;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GoogleListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return ['response' => 'onResponse'];
    }

    public function onResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        if (
            $response->isRedirection()
            || ($response->headers->has('content-Type') && !str_contains($response->headers->get('content-Type'), 'html'))
            || 'html' !== $event->getRequest()->getRequestFormat()
        ) {
            return;
        }

        $response->setContent($response->getContent() . "GA CODE");
    }
}
