<?php
namespace App\libs;
use \PDO;
/**Variables en .env
    HOST=""
	USER=""
	PASSWD=""
	DBNAME=""
 */


class Db
{
    private $pdo;
    private $sSQL;
	private $isConnected = false;
    private $parametros;
  
    public function __construct()
    {
        $this->Connect();
        $this->parametros = array();
    }
    
    private function Connect()
    {
        $dbname=$_ENV['DBNAME'];
        $host=$_ENV['HOST'];
        $pwd = $_ENV['PASSWD'];
        $usr = $_ENV['USER'];
        $dsn = 'mysql:dbname=' . $dbname . ';host=' . $host . '';
        
    
        $options = array(
            PDO::ATTR_PERSISTENT => false, 
            PDO::ATTR_EMULATE_PREPARES => false, 
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"
        );

        try {
			# Intentar la conexión 
            $this->pdo = new PDO($dsn, $usr, $pwd, $options);
                       
            # Conexión exitosa, asignar true a la variable booleana isConnected.
            $this->isConnected = true;
        }
        catch (PDOException $e) {
            # Escribir posibles excepciones en el error_log
            $this->error = $e->getMessage();
        }
    }

    /*
     *   Este método cierra la conexión
     *   No es obligatorio, ya que PHP la cierra cuando termina el script
     *   Ver: http://es.stackoverflow.com/questions/50083/50097#50097
     */

    public function closeConnection()
    {
        # Setea el objeto PDO a null para cerrar la conexion
        # http://www.php.net/manual/en/pdo.connections.php
        $this->pdo = null;
    }
    
    /**
     *	Método que será usado para enviar cualquier consulta a la BD.
     *	
     *	1. Si no hay conexión, conectar a la BD.
     *	2. Preparar la consulta.
     *	3. Parametrizar la consulta.
     *	4. Ejecutar la consulta.	
     *	5. Si ocurre una excepción: Escribirla en el archivo log junto con la consulta.
     *	6. Resetear los parámetros.
     */
     
    private function Init($sql, $parametros = "")
    {
        # Conecta a la BD
        if (!$this->isConnected) {
            $this->Connect();
        }
        try {
            # Preparar la consulta
            $this->sSQL = $this->pdo->prepare($sql);
            
            # Agregar parámetros a la matriz de parámetros	
            $this->bindMas($parametros);
            
            # Asignar parámetros
            if (!empty($this->parametros)) {
                foreach ($this->parametros as $param => $value) {
                    if(is_int($value[1])) {
                        $type = PDO::PARAM_INT;
                    } else if(is_bool($value[1])) {
                        $type = PDO::PARAM_BOOL;
                    } else if(is_null($value[1])) {
                        $type = PDO::PARAM_NULL;
                    } else {
                        $type = PDO::PARAM_STR;
                    }
                    // Añade el tipo cuando asigna los valores a la columna 
                    $this->sSQL->bindValue($value[0], $value[1], $type);
                }
            }
            
            # Ejecuta la consulta SQL 
            $this->sSQL->execute();
        }
        catch (PDOException $e) {
            # Escribe en el archivo log si ocurre un excepción
            echo $this->ExceptionLog($e->getMessage(), $sql);
            die();
        }
        
        # Resetea los parámetros
        $this->parametros = array();
    }
    
    /**
     *	@void 
     *
     *	Agrega un parámetro al arreglo de parámetros
     *	@param string $parametro  
     *	@param string $valor 
     */
    public function bind($parametro, $valor)
    {
        $this->parametros[sizeof($this->parametros)] = [":" . $parametro , $valor];
    }
    /**
     *	@void
     *	
     *	Agrega más parámetros al arreglo de parámetros
     *	@param array $parray
     */
    public function bindMas($parray)
    {
        if (empty($this->parametros) && is_array($parray)) {
            $columns = array_keys($parray);
            foreach ($columns as $i => &$column) {
                $this->bind($column, $parray[$column]);
            }
        }
    }
    /**
     *  Si la consulta SQL contiene un SELECT o SHOW, devolverá un arreglo conteniendo todas las filas del resultado
     *     Nota: Si se requieren otros tipos de resultados la clase puede modificarse, 
     *           agregandolos o se pueden crear otros métodos que devuelvan los resultados como los necesitemos
     *           en nuesta aplicación. Para tipos de resultados ver: http://php.net/manual/es/pdostatement.fetch.php 
     *	Si la consulta SQL es un DELETE, INSERT o UPDATE, retornará el número de filas afectadas
     *
     *  @param  string $sql
     *	@param  array  $params
     *	@param  int    $fetchmode
     *	@return mixed
     */

    public function query($sql, $params = null, $fetchmode = PDO::FETCH_ASSOC)
    {
        $sql = trim(str_replace("\r", " ", $sql));
        
        $this->Init($sql, $params);
        
        $rawStatement = explode(" ", preg_replace("/\s+|\t+|\n+/", " ", $sql));
        
        # Determina el tipo de SQL 
        $statement = strtolower($rawStatement[0]);
        
        if ($statement === 'select' || $statement === 'show') {
            return $this->sSQL->fetchAll($fetchmode);
        } elseif ($statement === 'insert' || $statement === 'update' || $statement === 'delete') {
            return $this->sSQL->rowCount();
        } else {
            return NULL;
        }
    }
}

?>