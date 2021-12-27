<?php


require __DIR__ . '/../vendor/autoload.php';


Dotenv\Dotenv::createImmutable (__DIR__."./..")->load();


$app = Slim\Factory\AppFactory::create();

require_once __DIR__ . ('/../src/rutas/rutas.php');

$app->run();