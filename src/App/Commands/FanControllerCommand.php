<?php

namespace Console\App\Commands;

use Console\App\Services\Shelly\ShellyService;
use Console\App\Services\SwitchBot\SwitchBotService;
use Monolog\Handler\TelegramBotHandler;
use Monolog\Logger;
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
        $logger = new Logger('log');
        $logger->pushHandler(new TelegramBotHandler($_ENV['TELEGRAM_API_KEY'], $_ENV['TELEGRAM_CHANNEL']));

        $output->writeln("Starting process...");
        $logger->info("Starting fan check.");

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
                    $result = $switchbot->runByDewpoint(function () use ($shelly, $output, $logger) {
                        $output->writeln("Turning fan on.");
                        $logger->info("Turing fan on");
                        $shelly->plugTurnOn();
                    }, function () use ($shelly, $output, $logger) {
                        $output->writeln("Turning fan off.");
                        $logger->info("Turing fan off");
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

            $logger->error('Command failed.');
            $logger->error($exception->getMessage());

            return Command::FAILURE;
        }

        if (!$result) {
            $logger->info('Error was returned.');
            return Command::FAILURE;
        }

        $logger->info('Job succeeded.');
        $output->writeln("Job succeeded.");
        return Command::SUCCESS;
    }
}
