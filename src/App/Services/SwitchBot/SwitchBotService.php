<?php

namespace Console\App\Services\SwitchBot;

use Console\App\Enums\SwitchBot\Command;
use Console\App\Enums\SwitchBot\Status;
use Console\App\Services\SwitchBot\Api\SwitchBotApiDeviceCommand;
use Console\App\Services\SwitchBot\Api\SwitchBotApiDeviceList;
use Console\App\Services\SwitchBot\Api\SwitchBotApiDeviceStatus;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class SwitchBotService
{
    private SwitchBotApiDeviceCommand $command;
    private SwitchBotApiDeviceList $list;
    private SwitchBotApiDeviceStatus $status;

    private string $botId;

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function __construct()
    {
        $this->command = new SwitchBotApiDeviceCommand();
        $this->list = new SwitchBotApiDeviceList();
        $this->status = new SwitchBotApiDeviceStatus();

        $this->botId = $this->getBotId();
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function botTurnOn(): bool
    {
        $status = $this->status->request(['id' => $this->botId]);
        if ($status['power'] === Status::On->value) {
            return true;
        }

        $this->command->execute(Command::TurnOn, $this->botId);

        return true;
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function botTurnOff(): bool
    {
        $status = $this->status->request(['id' => $this->botId]);
        if ($status['power'] === Status::Off->value) {
            return true;
        }

        $this->command->execute(Command::TurnOff, $this->botId);

        return true;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    private function getBotId(): string
    {
        $list = $this->list->request();

        $key = array_search('Bot', array_column($list, 'deviceType'));
        if (!$key || empty($list[$key]['deviceId'])) {
            throw new \Exception("Bot is not found in devicelist.");
        }

        return $list[$key]['deviceId'];
    }
}



