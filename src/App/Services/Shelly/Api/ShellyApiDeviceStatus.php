<?php

namespace Console\App\Services\Shelly\Api;

use Console\App\Services\SwitchBot\Api\SwitchBotApi;
use Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ShellyApiDeviceStatus extends ShellyApi
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

        return $this->fetch(self::HTTP_METHOD_GET, "device/status", $options);
    }

    protected function formatResult(array $content): ?array
    {
        return $content['data'] ?? null;
    }
}
