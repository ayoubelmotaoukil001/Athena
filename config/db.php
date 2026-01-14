<?php
class db
{
    private $server = "localhost";
    private $user = "root";
    private $password = "";
    private $db_name = "athena";
    private $port = "3308";

    private static $instance = null;
    private $connection;

    private function __construct()
    {
        try {
            $this->connection = new PDO(
                "mysql:host=" . $this->server .
                ";port=" . $this->port .
                ";dbname=" . $this->db_name .
                ";charset=utf8mb4",
                $this->user,
                $this->password
            );

            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
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

    private function __clone() {}
    public function __wakeup() {}
}
?>