<?php
declare(strict_types=1);
namespace LegacyLab\Core;

use LegacyLab\Core\Container;

final class Bootstrap
{
    public static function container(): Container
    {
        $config = require __DIR__ . '/../../setup/config.php';
        return new Container($config);
    }
}