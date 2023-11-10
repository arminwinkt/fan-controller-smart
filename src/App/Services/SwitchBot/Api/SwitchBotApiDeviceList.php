<?php

namespace Console\App\Services\SwitchBot\Api;

class SwitchBotApiDeviceList extends SwitchBotApi
{
    public function request(?array $options = null): ?array
    {
        return $this->fetch(self::HTTP_METHOD_GET, 'devices');
    }

    protected function formatResult(array $content): ?array
    {
        return $content['body']['deviceList'] ?? null;
    }

    public function findDevice(string $id, array $list, string $column = 'deviceId'): ?array
    {
        $key = array_search($id, array_column($list, $column));
        if ($key === false || empty($list[$key][$column])) {
            return null;
        }

        return $list[$key];
    }

}



