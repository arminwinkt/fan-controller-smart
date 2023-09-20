<?php

namespace Console\App\Services\SwitchBot\Api;


use Exception;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class SwitchBotApi
{
    protected const BASE_URL = 'https://api.switch-bot.com/v1.1/';
    protected const HTTP_METHOD_GET = 'GET';
    protected const HTTP_METHOD_POST = 'POST';
    protected HttpClientInterface $httpClient;

    public function __construct()
    {
        $this->httpClient = $this->createHttpClient();
    }

    protected function createHttpClient(): HttpClientInterface
    {
        $token = $_ENV['TOKEN'];
        $secret = $_ENV['SECRET'];
        $nonce = Uuid::v4();
        $t = time() * 1000;
        $data = utf8_encode($token . $t . $nonce);
        $sign = hash_hmac('sha256', $data, $secret, true);
        $sign = strtoupper(base64_encode($sign));

        return HttpClient::create([
            'headers' => [
                "Authorization:" . $token,
                "sign:" . $sign,
                "nonce:" . $nonce,
                "t:" . $t
            ]
        ]);
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
        $url = self::BASE_URL . $url;
        $options = [];
        if ($payload) {
            $options['json'] = $payload;
        }
        $response = $this->httpClient->request($method, $url, $options);

        if ($response->getStatusCode() !== 200) {
            throw new Exception("SwitchBotApi Http Code does not return 200 for $method $url. (Status code: {$response->getStatusCode()})");
        }

        $content = $response->toArray();
        if ($content['statusCode'] !== 100) {
            throw new Exception("SwitchBotApi result does not return status 100 for $url");
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
