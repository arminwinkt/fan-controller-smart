<?php

namespace Console\App\Services\SwitchBot\Api;

use Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class SwitchBotApiDeviceStatus extends SwitchBotApi
{
    public const ON = 'on';
    public const OFF = 'off';

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function request(?array $options = null): ?array
    {
        $id = $options['id'] ?? null;
        if (empty($id)) {
            throw new Exception("Missing argument `id`");
        }

        return $this->fetch(self::HTTP_METHOD_GET, "devices/$id/status");
    }

    protected function formatResult(array $content): ?array
    {
        return $content['body'] ?? null;
    }
}
