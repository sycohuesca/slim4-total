<?php


require __DIR__ . '/../vendor/autoload.php';


Dotenv\Dotenv::createImmutable (__DIR__."./..")->load();


$app = Slim\Factory\AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
// Cambiar a false en produccion
$app->addErrorMiddleware(true, true, true);
$app->add(new App\midelwares\Cors());

require_once __DIR__ . ('/../src/rutas/rutas.php');

$app->run();