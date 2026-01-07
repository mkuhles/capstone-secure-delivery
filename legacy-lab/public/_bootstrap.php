<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
use LegacyLab\Core\Bootstrap;

$container = Bootstrap::container();
$container->requestId()->initFromGlobals((array)($container->config('trusted_proxies') ?? []));
$container->logger()->info('request.start');

$security = $container->securityHeaders()->apply($container->config() ?? []);
$container->setCspNonce($security['csp_nonce']); // pass to view if needed

return $container;