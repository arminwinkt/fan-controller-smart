<?php

namespace Console\App\Commands;

use Console\App\Services\Shelly\ShellyService;
use Console\App\Services\SwitchBot\SwitchBotService;
use Console\App\Services\TelegramLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class FanControllerCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('fan:controller')
            ->setDescription('Controls the fan')
            ->setHelp('Controls the fan. Use mode for different options.')
            ->addArgument('mode', InputArgument::OPTIONAL, 'auto,start,stop.', 'auto');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $output->writeln("Starting process...");
        TelegramLogger::getInstance()->log("Starting fan check");

        $shelly = new ShellyService();
        $switchbot = new SwitchBotService();
        $result = false;

        try {
            switch ($input->getArgument('mode')) {
                case 'start':
                    $result = $shelly->plugTurnOn();
                    break;
                case 'stop':
                    $result = $shelly->plugTurnOff();
                    break;
                case 'auto':
                    $result = $switchbot->runByDewpoint(function () use ($shelly, $output) {
                        $output->writeln("Turning fan ON.");
                        TelegramLogger::getInstance()->log("Turing fan ON");
                        $shelly->plugTurnOn();
                    }, function () use ($shelly, $output) {
                        $output->writeln("Turning fan OFF");
                        TelegramLogger::getInstance()->log("Turing fan OFF");
                        $shelly->plugTurnOff();
                    });
                    break;
                default:
                    $output->writeln("Argument `mode` does not exist");
                    break;
            }
        } catch (\Throwable $exception) {
            $output->writeln("Command failed.");
            $output->writeln($exception->getMessage());

            TelegramLogger::getInstance()->log('Command failed', 'error');
            TelegramLogger::getInstance()->log($exception->getMessage(), 'error');

            TelegramLogger::getInstance()->log("Turning Fan OFF", 'info');
            $shelly->plugTurnOff();

            return Command::FAILURE;
        }

        if (!$result) {
            TelegramLogger::getInstance()->log('Error was returned', 'waring');
            return Command::FAILURE;
        }

        TelegramLogger::getInstance()->log('Job succeeded');
        $output->writeln("Job succeeded");
        return Command::SUCCESS;
    }
}
