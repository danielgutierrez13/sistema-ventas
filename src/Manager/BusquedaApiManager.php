<?php

declare(strict_types=1);

namespace Pidia\Apps\Demo\Manager;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class BusquedaApiManager
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    public function dni(string $dni): ?array
    {
        $response = $this->httpClient->request(
            'GET',
            'https://api.apis.net.pe/v1/dni?numero='.$dni
        );

        $statusCode = $response->getStatusCode();

        if (200 === $statusCode) {
            $contentType = $response->getHeaders()['content-type'][0];
            $content = $response->getContent();
            $content = $response->toArray();
            return $content;
        }
        return null;
    }

    public function ruc(string $ruc): ?array
    {
        $response = $this->httpClient->request(
            'GET',
            'https://api.apis.net.pe/v1/ruc?numero='.$ruc
        );

        $statusCode = $response->getStatusCode();

        if (200 === $statusCode) {
            $contentType = $response->getHeaders()['content-type'][0];
            $content = $response->getContent();
            $content = $response->toArray();
            return $content;
        }
        return null;
    }
}
