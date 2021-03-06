<?php
/**
 * PHP version 7.0
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Ewallet\Slim\Application;

chdir(__DIR__ . '/../'); // Match the value in .env.test, which is a relative path

$environment = new Dotenv(__DIR__ . '/../', '.env.tests');
$environment->load();
$environment->required(['APP_ENV', 'DB_URL']);

$app = new Application(require __DIR__ . '/../config.php');
$app->run();
