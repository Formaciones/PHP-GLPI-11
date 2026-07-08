<?php

namespace Plugin\SubcontractInstall;

final class Logger
{
    private const LOG_FILE = 'subcontractinstall';

    public static function info(string $message): void
    {
        \Toolbox::logInFile(
            self::LOG_FILE,
            '[INFO] ' . $message . PHP_EOL
        );
    }

    public static function warning(string $message): void
    {
        \Toolbox::logInFile(
            self::LOG_FILE,
            '[WARNING] ' . $message . PHP_EOL
        );
    }

    public static function error(string $message): void
    {
        \Toolbox::logInFile(
            self::LOG_FILE,
            '[ERROR] ' . $message . PHP_EOL
        );
    }
}