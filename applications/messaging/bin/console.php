<?php
/**
 * PHP version 7.0
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
require __DIR__ . '/../vendor/autoload.php';

use Ewallet\Pimple\EwalletMessagingContainer;
use Ewallet\SymfonyConsole\EwalletApplication;
use Dotenv\Dotenv;

$environment = new Dotenv(__DIR__ . '/../');
$environment->load();
$environment->required([
    'APP_ENV',
    'MYSQL_USER',
    'MYSQL_PASSWORD',
    'MYSQL_HOST',
    'RABBIT_MQ_USER',
    'RABBIT_MQ_PASSWORD',
    'RABBIT_MQ_HOST'
]);

$application = new EwalletApplication($container = new EwalletMessagingContainer(
    require __DIR__ . '/../config.php'
));

$application->run();
