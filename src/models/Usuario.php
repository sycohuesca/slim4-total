<?php
namespace App\models;
use \Firebase\JWT\JWT;
use App\libs\Model;

class Usuario extends Model
{
    // Clave primaria siempre id

    protected $table="user";
    protected $campos=array(
         "nombre",
         "apellidos",
         "unidad",
         "email"
     );

     function login($request, $response,  $args){
        $carga = array(
            "iss" => "http://example.org",
            "aud" => "http://example.com",
            "iat" => time(),
            "exp"=> time()+(60*60*24*10),
            "nbf" => 1357000000
        );
        $token=JWT::encode($carga, $_ENV['SECRET']);
         $response->getBody()->write(json_encode(['token'=>$token]));
        return $response;
     }
     
     function check($request, $response,  $args){
         $token=$request->getAttribute('token');
         $response->getBody()->write(json_encode(['token'=>$token]));
         return $response;
     }

}
?>