<?php

namespace Console\App\Services\Shelly;

use Console\App\Enums\Shelly\Relay;
use Console\App\Services\Shelly\Api\ShellyApiDeviceRelay;
use Console\App\Services\Shelly\Api\ShellyApiDeviceStatus;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ShellyService
{
    private ShellyApiDeviceStatus $status;
    private ShellyApiDeviceRelay $relay;


    public function __construct()
    {
        $this->relay = new ShellyApiDeviceRelay();
        $this->status = new ShellyApiDeviceStatus();
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function plugTurnOn(): bool
    {
        $status = $this->status->request(['id' => $_ENV['SHELLY_PLUG_ID']]);

        if (!$status['online']) {
            return false;
        }

        sleep(2);

        $this->relay->execute(Relay::TurnOn, $_ENV['SHELLY_PLUG_ID']);

        return true;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function plugTurnOff(): bool
    {
        $status = $this->status->request(['id' => $_ENV['SHELLY_PLUG_ID']]);

        if (!$status['online']) {
            return false;
        }

        sleep(2);

        $this->relay->execute(Relay::TurnOff, $_ENV['SHELLY_PLUG_ID']);

        return true;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function plugToggle(): bool
    {
        $status = $this->status->request(['id' => $_ENV['SHELLY_PLUG_ID']]);

        if (!$status['online']) {
            return false;
        }

        sleep(2);

        $this->relay->execute(Relay::Toggle, $_ENV['SHELLY_PLUG_ID']);

        return true;
    }

}



