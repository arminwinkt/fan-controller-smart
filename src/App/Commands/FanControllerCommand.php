<?php

namespace Console\App\Commands;

use Console\App\Services\SwitchBot\Api\SwitchBotApiDeviceList;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Uid\Uuid;


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

        $test = new SwitchBotApiDeviceList();
        var_dump($test->request());

        die();



        $url = 'https://api.switch-bot.com/v1.1/devices/D61252CF00A6/commands';
        $curl2 = curl_init($url);
        curl_setopt($curl2, CURLOPT_URL, $url);
        curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl2, CURLOPT_POST, 1);

        $headers = array(
            "Content-Type:application/json",
            "Authorization:" . $token,
            "sign:" . $sign,
            "nonce:" . $nonce,
            "t:" . $t
        );

        curl_setopt($curl2, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl2, CURLOPT_POSTFIELDS, json_encode([
            'command' => 'turnOff', // 'turnOn'
            'parameter' => 'default',
            'commandType' => 'command',
        ]));

        $response = curl_exec($curl2);
        curl_close($curl2);

        var_dump($response);


        $output->writeln($input->getArgument('mode'));
        return Command::SUCCESS;
    }
}



