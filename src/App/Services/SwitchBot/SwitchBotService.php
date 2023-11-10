<?php

namespace Console\App\Services\SwitchBot;

use Console\App\Enums\SwitchBot\Command;
use Console\App\Enums\SwitchBot\Status;
use Console\App\Services\SwitchBot\Api\SwitchBotApiDeviceCommand;
use Console\App\Services\SwitchBot\Api\SwitchBotApiDeviceList;
use Console\App\Services\SwitchBot\Api\SwitchBotApiDeviceStatus;
use Console\App\Services\Temperature\DewPointCalculationService;
use Exception;
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

    private array $deviceList;
    private string $botId;

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function __construct()
    {
        $this->command = new SwitchBotApiDeviceCommand();
        $this->list = new SwitchBotApiDeviceList();
        $this->status = new SwitchBotApiDeviceStatus();

        $this->deviceList = $this->list->request();
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
     * @throws Exception
     */
    private function getBotId(): string
    {
        $device = $this->list->findDevice('Bot', $this->deviceList, 'deviceType');
        if (!$device) {
            throw new Exception("Bot is not found in devicelist.");
        }

        return $device['deviceId'];
    }

    /**
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function botAuto(): bool
    {
        $deviceOutdoor = $this->list->findDevice(METER_OUTDOOR, $this->deviceList);
        if (!$deviceOutdoor) {
            throw new Exception("Outdoor meter is not found in devicelist.");
        }
        $deviceIndoor = $this->list->findDevice(METER_INDOOR, $this->deviceList);
        if (!$deviceIndoor) {
            throw new Exception("Indoor meter is not found in devicelist.");
        }

        $statusOutdoor = $this->status->request(['id' => $deviceOutdoor['deviceId']]);
        $statusIndoor = $this->status->request(['id' => $deviceIndoor['deviceId']]);

        $calculate = new DewPointCalculationService($statusOutdoor, $statusIndoor);
        if ($calculate->calculate()) {
            var_dump('TURN ONNNN');
            $this->botTurnOn();
            return true;
        }

        var_dump('TURN OFFFF');
        $this->botTurnOff();
        return true;
    }
}



