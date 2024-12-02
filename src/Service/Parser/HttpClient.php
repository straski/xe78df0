<?php

namespace App\Service\Parser;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpOptions;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\{HttpClient\HttpClientInterface};

class HttpClient
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private HttpClientInterface $http,
        private readonly string $notifyUrl
    ) {
        $this->http = $http->withOptions(
            (new HttpOptions())
                ->setBaseUri($this->notifyUrl)
                ->setHeader('content-type', 'application/json')
                ->toArray()
        );
    }

    public function sendNotification(string $fileId): bool
    {
        try {
            $response = $this->http->request('POST', '', ['body' => ['fileId' => $fileId]]);
            if ($response->getStatusCode() === 200) {
                $this->logger->info("Parser notified about $fileId");
            }
        } catch (TransportExceptionInterface $e) {
            $this->logger->info("Failed to notify parser about $fileId");
            $this->logger->info($e->getMessage());
            return false;
        }

        return true;
    }
}
