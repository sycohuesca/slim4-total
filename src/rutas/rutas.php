<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\models\Usuario;
use App\midelwares\Auth;
use App\midelwares\Cors;


$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
// Cambiar a false en produccion
$app->addErrorMiddleware(true, true, true);
$app->add(new Cors());






$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("hello world!");
    return $response;
});
$app->get('/login', Usuario::class.':login');


$app->group('/users', function ($group) {
    $group->get('', Usuario::class . ':getAll');
    $group->get('/{id}', Usuario::class . ':find');
    $group->post('', Usuario::class . ':create');
    $group->put('/{id}', Usuario::class . ':update');
    $group->delete('/{id}', Usuario::class . ':delete'); 
})->add(new Auth());


$app->get('/email/{email}', function (Request $request, Response $response, $args){
    $para      = $args['email'];
    $mensaje = "Esto es una prueba 1\r\nA ver si te llega correctamente 2\r\nUn saludo 3\r\n\n\n\nwww.ejemplocodigo.com";

    // Si cualquier línea es más larga de 70 caracteres, se debería usar wordwrap()
    
    
    // Enviamos el email
    
try {
    mail($para, 'Probando la funcion MAIL desde PHP', $mensaje);
    $data=["mensaje"=>"Email enviado","email"=>$para];
} catch (Exception $e) {
   $data=["mensaje"=>"Email no  enviado"];
}


     
     $response->getBody()->write(json_encode($data));
      return $response
               ->withHeader('Content-Type', 'application/json')
               ->withStatus(200)
               ->withHeader('Access-Control-Allow-Origin', '*')
             ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
             ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
      
 });




?>