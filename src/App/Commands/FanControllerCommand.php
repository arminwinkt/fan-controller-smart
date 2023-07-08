<?php

namespace Console\App\Commands;

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
        $token = $_ENV['TOKEN'];
        $secret = $_ENV['SECRET'];
        $nonce = Uuid::v4();
        $t = time() * 1000;
        $data = utf8_encode($token . $t . $nonce);
        $sign = hash_hmac('sha256', $data, $secret, true);
        $sign = strtoupper(base64_encode($sign));


        $client = HttpClient::create([
            'headers' => [
                "Content-Type:application/json",
                "Authorization:" . $token,
                "sign:" . $sign,
                "nonce:" . $nonce,
                "t:" . $t
            ]
        ]);
        $response = $client->request('GET', 'https://api.switch-bot.com/v1.1/devices');
        $statusCode = $response->getStatusCode();
        $content = $response->toArray();

        var_dump($statusCode);
        var_dump($content);
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



