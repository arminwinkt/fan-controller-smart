<?php

namespace Console\App\Services\SwitchBot\Api;

use Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class SwitchBotApiDeviceCommand extends SwitchBotApi
{
    public const TURN_OFF = 'turnOff';
    public const TURN_ON = 'turnOn';
    public const PRESS = 'press';

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function request(?array $options = null): ?array
    {
        $command = $options['command'] ?? null;
        if (empty($command)) {
            throw new Exception("Missing argument `command`");
        }
        $id = $options['id'] ?? null;
        if (empty($id)) {
            throw new Exception("Missing argument `id`");
        }

        return $this->fetch(self::HTTP_METHOD_POST, "devices/$id/command", [
            'command' => $command,
        ]);
    }

    protected function formatResult(array $content): ?array
    {
        return $content['body'] ?? null;
    }
}
