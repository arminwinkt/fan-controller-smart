<?php

namespace Console\App\Services\Shelly\Api;

use Console\App\Enums\Shelly\Relay;
use Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ShellyApiDeviceRelay extends ShellyApi
{
    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function request(?array $options = null): ?array
    {
        $id = $options['id'] ?? null;
        if (empty($id)) {
            throw new Exception("Missing argument `id`");
        }

        $options['channel'] = 0;

        return $this->fetch(self::HTTP_METHOD_POST, "device/relay/control", $options);
    }

    protected function formatResult(array $content): ?array
    {
        return $content['data'] ?? null;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function execute(Relay $relay, string $id): ?array
    {
        return $this->request(['turn' => $relay->value, 'id' => $id]);
    }
}
