<?php

namespace Console\App\Services\Shelly\Api;


use Exception;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class ShellyApi
{
    protected const BASE_URL = 'https://shelly-111-eu.shelly.cloud/';
    protected const HTTP_METHOD_GET = 'GET';
    protected const HTTP_METHOD_POST = 'POST';
    protected HttpClientInterface $httpClient;

    public function __construct()
    {
        $this->httpClient = $this->createHttpClient();
    }

    public function createHttpClient(): HttpClientInterface
    {
        return HttpClient::create();
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    protected function fetch(string $method, string $url, ?array $payload = null): array
    {
        $options = [
            'auth_key' => $_ENV['SHELLY_KEY'],
        ];

        $url = self::BASE_URL . $url;
        $response = $this->httpClient->request($method, $url, [
            'query' => array_merge($options, $payload),
            'body' => array_merge($options, $payload),
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new Exception("Shelly Http Code does not return 200 for $method $url. (Status code: {$response->getStatusCode()})");
        }

        $content = $response->toArray();

        if (!$content['isok']) {
            throw new Exception("Shelly response is not `isok` for $method $url. (Status code: {$response->getStatusCode()})");
        }

        return $this->formatResult($content);
    }


    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public abstract function request(?array $options = null): ?array;

    protected abstract function formatResult(array $content): ?array;
}
