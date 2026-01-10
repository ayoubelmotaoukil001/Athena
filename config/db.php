<?php
class db{
   private $server= "localhost" ;
   private $user ="root" ;
   private  $password =""  ;
   private $db_name ="athena" ;
   private $port = "3308" ;

   private static $instance = null;
   private $connection  ;
  private function __construct()
{
    $this->connection = new PDO(
        "mysql:host=".$this->server.
        ";port=".$this->port.
        ";dbname=".$this->db_name.
        ";charset=utf8",
        $this->user,
        $this->password
    );

    $this->connection->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );
}

public static function getInstance()
{
    if (self::$instance === null) {
        self::$instance = new self();
    }
    return self::$instance;
}
public function getConnection()
{
    return $this->connection;
}


}

?>