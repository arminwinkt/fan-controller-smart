<?php

namespace Console\App\Commands;

use Console\App\Services\SwitchBot\SwitchBotService;
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

        $service = new SwitchBotService();
        $result = false;

        switch ($input->getArgument('mode')) {
            case 'start':
                $result = $service->botTurnOn();
                break;
            case 'stop':
                $result = $service->botTurnOff();
                break;
            case 'auto':
                //$result = $service->botTurnOn();
                break;
            default:
                $output->writeln("Argument `mode` does not exist");
                break;
        }

        if (!$result) {
            return Command::FAILURE;
        }

        $output->writeln("Job succeeded.");
        return Command::SUCCESS;

    }
}
