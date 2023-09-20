<?php

namespace Console\App\Services\SwitchBot;

use Console\App\Services\SwitchBot\Api\SwitchBotApiDeviceCommand;
use Console\App\Services\SwitchBot\Api\SwitchBotApiDeviceList;
use Console\App\Services\SwitchBot\Api\SwitchBotApiDeviceStatus;

class SwitchBotService
{
    private SwitchBotApiDeviceCommand $command;
    private SwitchBotApiDeviceList $list;
    private SwitchBotApiDeviceStatus $status;

    private string $botId;

    public function __construct()
    {
        $this->command = new SwitchBotApiDeviceCommand();
        $this->list = new SwitchBotApiDeviceList();
        $this->status = new SwitchBotApiDeviceStatus();

        $this->botId = $this->getBotId();
    }

    public function botTurnOn(): bool
    {
        $status = $this->status->request(['id' => $this->botId]);
        if ($status['power'] === SwitchBotApiDeviceStatus::ON) {
            return true;
        }

        $this->command->request(['command' => SwitchBotApiDeviceCommand::TURN_ON, 'id' => $this->botId]);

        return true;
    }

    public function botTurnOff(): bool
    {
        $status = $this->status->request(['id' => $this->botId]);
        if ($status['power'] === SwitchBotApiDeviceStatus::OFF) {
            return true;
        }

        $this->command->request(['command' => SwitchBotApiDeviceCommand::TURN_OFF, 'id' => $this->botId]);

        return true;
    }

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



