<?php
/**
 * PHP version 5.6
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
require __DIR__ . '/../../../../../../vendor/autoload.php';

$options = require __DIR__ . '/../../../../../../app/config.php';

$app = new \Slim\Slim($options['slim']);
$app->add(
    new \EwalletApplication\Bridges\Slim\Middleware\RequestLoggingMiddleware()
);

$resolver = new \ComPHPPuebla\Slim\Resolver();
$services = new \EwalletApplication\Bridges\Slim\Services($resolver, $options);
$services->configure($app);

$controllers = new \EwalletApplication\Bridges\Slim\Controllers($resolver);
$controllers->register($app);

$app->run();
