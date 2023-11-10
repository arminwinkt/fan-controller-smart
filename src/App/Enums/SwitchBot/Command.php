<?php

namespace Console\App\Enums\SwitchBot;

enum Command: string
{
    case TurnOff = 'turnOff';
    case TurnOn = 'turnOn';
    case Press = 'press';
}
