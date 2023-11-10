<?php

namespace Console\App\Services\Temperature;

class DewPointCalculationService
{

    private float $temperatureOutdoor;
    private float $temperatureIndoor;
    private int $humidityOutdoor;
    private int $humidityIndoor;

    public function __construct(array $dataOutdoor, array $dataIndoor)
    {
        if (empty($dataOutdoor) || !isset($dataOutdoor['humidity']) || !isset($dataOutdoor['temperature'])) {
            throw new \Exception("Outdoor data is not complete.");
        }
        if (empty($dataIndoor) || !isset($dataIndoor['humidity']) || !isset($dataIndoor['temperature'])) {
            throw new \Exception("Outdoor data is not complete.");
        }

        $this->temperatureOutdoor = (float)$dataOutdoor['temperature'];
        $this->temperatureIndoor = (float)$dataIndoor['temperature'];
        $this->humidityOutdoor = (int)$dataOutdoor['humidity'];
        $this->humidityIndoor = (int)$dataIndoor['humidity'];
    }

    /**
     * checks if the dew point is larger outdoor than indoors
     * and therefor start the fan
     */
    public function calculate(): bool
    {
        if ($this->temperatureOutdoor < TEMPS_MIN_OUTDOOR) {
            return false;
        }

        if ($this->temperatureIndoor < TEMPS_MIN_INDOOR) {
            return false;
        }

        $dewPointOutdoor = $this->calculateDewPoint($this->temperatureOutdoor, $this->humidityOutdoor);
        $dewPointIndoor = $this->calculateDewPoint($this->temperatureIndoor, $this->humidityIndoor);
        $deltaDewPoint = $dewPointIndoor - $dewPointOutdoor;

        if (TEMPS_SWITCH_OFF + TEMPS_HYSTERESE < $deltaDewPoint) {
            return true;
        }

        return false;
    }

    private function calculateDewPoint(float $temperature, int $humidity): float
    {
        if ($temperature >= 0) {
            $a = 7.5;
            $b = 237.3;
        } else {
            $a = 7.6;
            $b = 240.7;
        }

        // Saturation vapor pressure in hPa
        $sdd = 6.1078 * pow(10, ($a * $temperature) / ($b + $temperature));

        // Steam preassure in hPa
        $dd = $sdd * ($humidity / 100);

        // v-parameter
        $v = log10($dd / 6.1078);

        // Dew point temperature (°C)
        return ($b * $v) / ($a - $v);
    }
}
