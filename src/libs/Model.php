<?php
namespace App\libs;
use App\libs\Almacen;

  class Model
{
         private $sqlSelect, $sqlFind, $sqlCreate, $sqlUpdate, $sqlDelete;
         protected $table, $campos;

         function __construct(){
                $this->init();            
             }
             
       private function  init(){
        $camposCreate=implode(", ",$this->campos);
        foreach ($this->campos as $key ) {
            $camposValue[]=":".$key;
            $camposUpdate[]=$key."=:".$key;
        }
        $camposValue=implode(", ",$camposValue);
        $camposUpdate=implode(", ",$camposUpdate);
        $this->sqlSelect = "SELECT * FROM {$this->table}";
        $this->sqlFind = "SELECT *  FROM {$this->table} WHERE id=:id";
        $this->sqlCreate =  "INSERT INTO {$this->table} ($camposCreate) VALUES ($camposValue)";
        $this->sqlUpdate = "UPDATE {$this->table} SET $camposUpdate WHERE id= :id";
        $this->sqlDelete= "DELETE FROM {$this->table} where id=:id";
       }      
            
      function getAll($request, $response)
    {       
        $consulta= new Almacen();
        $data= $consulta->query($this->sqlSelect);
        $response->getBody()->write(json_encode($data));
        return $response->withStatus(200);  
    }
    
     function create($request, $response, $args)
    {
        $body = $request->getParsedBody();
        $consulta= new Almacen();
        $data = $consulta->query($this->sqlCreate, $body);
        $response->getBody()->write(json_encode($data));
        return $response->withStatus(201);
    }

     function update ($request, $response, $args){
        $body = $request->getParsedBody();
        $body['id']=$args['id'];
        $consulta= new Almacen();
        $data = $consulta->query($this->sqlUpdate, $body);
        $response->getBody()->write(json_encode($data));
        return $response->withStatus(201);
    }

     function find($request, $response, $args)
    {
        $consulta=new Almacen();
        $datos = array("id"=>$args['id']);
        $data = $consulta->query($this->sqlFind, $datos);    
        $response->getBody()->write(json_encode($data));
        return $response->withStatus(200);
        
    }
     function delete($request, $response, $args)
    {
        $consulta=new Almacen();
        $datos = array("id"=>$args['id']);
        $data = $consulta->query($this->sqlDelete, $datos);    
        $response->getBody()->write(json_encode($data));
        return $response->withStatus(200);                
    }
   
}
?>