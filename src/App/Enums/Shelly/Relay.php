<?php

namespace Console\App\Enums\Shelly;

enum Relay: string
{
    case TurnOn = 'on';
    case TurnOff = 'off';
    case Toggle = 'toggle';
}
