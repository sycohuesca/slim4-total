<?php
namespace App\midelwares;
use \Firebase\JWT\JWT;
use Slim\Psr7\Response;

class Auth
{
    public function __invoke($request, $handler)
    {
       
try {
    $token =  $request->getServerParams()["HTTP_AUTHORIZATION"];  
    if($token){
            $deco=JWT::decode($token,$_ENV['SECRET'],array('HS256'));   
            $request=$request->withAttribute('token',$deco);                
    }
    else {
        throw new \Exception();
    }
    $response = $handler->handle($request); 
    
} catch (\Exception  $th) {
    $response= new Response();
    $response->getBody()->write(json_encode(['error'=>'Token no valido']));
}

return $response;
           
    }   
}
?>