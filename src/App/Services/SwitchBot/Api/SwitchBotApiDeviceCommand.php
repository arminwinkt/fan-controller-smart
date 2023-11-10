<?php

namespace Console\App\Services\SwitchBot\Api;

use Console\App\Enums\SwitchBot\Command;
use Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class SwitchBotApiDeviceCommand extends SwitchBotApi
{
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

        return $this->fetch(self::HTTP_METHOD_POST, "devices/$id/commands", [
            'command' => $command,
            'parameter' => 'default',
            'commandType' => 'command',
        ]);
    }

    protected function formatResult(array $content): ?array
    {
        return $content['body'] ?? null;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function execute(Command $command, string $id): ?array
    {
        return $this->request(['command' => $command->value, 'id' => $id]);
    }
}
