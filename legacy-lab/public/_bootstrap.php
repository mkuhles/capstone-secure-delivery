<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
use LegacyLab\Core\Bootstrap;

$container = Bootstrap::container();
$requestId = $container->requestId()->initFromGlobals((array)($container->config('trusted_proxies') ?? []));
$container->logger()->info('request.start');

return [$container, $requestId];