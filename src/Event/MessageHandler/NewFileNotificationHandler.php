<?php

namespace App\Event\MessageHandler;

use App\Event\Message\NewFileNotification;
use App\Service\Parser;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class NewFileNotificationHandler
{
    public function __construct(protected Parser\HttpClient $parserClient)
    {
    }

    public function __invoke(NewFileNotification $message)
    {
        $this->parserClient->sendNotification($message->getContent());
    }
}
