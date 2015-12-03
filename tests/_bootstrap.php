<?php
use Dotenv\Dotenv;
use Codeception\Util\Autoload;

Autoload::addNamespace('Page', __DIR__. '/_support/_pages');

$environment = new Dotenv(__DIR__ . '/../');
$environment->load();
$environment->required([
    'DOCTRINE_DEV_MODE',
    'TWIG_DEBUG',
    'SMTP_HOST',
    'SMTP_PORT',
    'MYSQL_USER',
    'MYSQL_PASSWORD',
    'MYSQL_HOST',
]);
