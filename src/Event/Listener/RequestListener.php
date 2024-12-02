<?php

namespace App\Event\Listener;

use Symfony\Component\{HttpKernel\Event\RequestEvent};

class RequestListener
{
    public function onKernelRequest(RequestEvent $event): void
    {
        $data = [];

        if (!$event->isMainRequest()) {
            return;
        }
    }
}
