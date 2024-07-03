<?php

namespace Console\App\Services;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\TelegramBotHandler;
use Monolog\Logger;

class TelegramLogger
{
    private static ?self $instance = null;

    private Logger $logger;

    public static function getInstance(): self
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->logger = new Logger('log');

        $formatter = new LineFormatter(null, "Y-m-d H:i:s", true, true);
        $handler = new TelegramBotHandler($_ENV['TELEGRAM_API_KEY'], $_ENV['TELEGRAM_CHANNEL']);
        $handler->setFormatter($formatter);
        $this->logger->pushHandler($handler);

    }

    public function log(string $message, string $level = 'info'): void
    {
        $this->logger->log($level, "\n" . $message);
    }

}