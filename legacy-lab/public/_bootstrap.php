<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
use LegacyLab\Core\Bootstrap;

$container = Bootstrap::container();
$requestId = $container->requestId()->initFromGlobals((array)($container->config('trusted_proxies') ?? []));
$container->logger()->info('request.start');

if (!empty($container->config('security_headers')['x_frame_options'])) {
    header('X-Frame-Options: DENY');
}


return [$container, $requestId];