<?php


require __DIR__ . '/../vendor/autoload.php';


Dotenv\Dotenv::createImmutable (__DIR__."./..")->load();


$app = Slim\Factory\AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
// Cambiar a false en produccion
$app->addErrorMiddleware(true, true, true);
$app->add(new App\midelwares\Cors());

$routers=require __DIR__ . ('/../src/rutas/rutas.php');
$routers($app);

$app->run();