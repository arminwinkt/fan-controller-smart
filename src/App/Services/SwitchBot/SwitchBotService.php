<?php

namespace Console\App\Services\SwitchBot;

use Console\App\Enums\SwitchBot\Command;
use Console\App\Enums\SwitchBot\Status;
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
        if ($status['power'] === Status::On->value) {
            return true;
        }

        $this->command->execute(Command::TurnOn, $this->botId);

        return true;
    }

    public function botTurnOff(): bool
    {
        $status = $this->status->request(['id' => $this->botId]);
        if ($status['power'] === Status::Off->value) {
            return true;
        }

        $this->command->execute(Command::TurnOff, $this->botId);

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



