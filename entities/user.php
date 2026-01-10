<?php
require_once __DIR__ . '/../config/db.php';

abstract class user
{
    protected $userid;
    protected $nom;
    protected $email;
    protected $password;
    protected $role;
    protected $connection;

    public function __construct()
    {
        $this->connection = db::getInstance()->getConnection();
    }

    // setters
    public function set_nom($nom)
    {
        $this->nom = $nom;
    }

    public function set_email($email)
    {
        $this->email = $email;
    }

    public function set_password($password)
    {
        $this->password = $password;
    }

    public function set_role($role)
    {
        $this->role = $role;
    }

    // getters
    public function get_userid()
    {
        return $this->userid;
    }

    public function get_nom()
    {
        return $this->nom;
    }

    public function get_email()
    {
        return $this->email;
    }

    public function get_role()
    {
        return $this->role;
    }

    // create user
    public function create()
    {
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        $stm = $this->connection->prepare(
            "insert into user (nom, email, password, role) values (?, ?, ?, ?)"
        );
        $stm->execute([$this->nom, $this->email, $this->password, $this->role]);
        $this->userid = $this->connection->lastInsertId();
        return $this->userid;
    }

    // update profile
    public function update_prof($new_password = null)
    {
        if ($new_password !== null) {
            $this->password = password_hash($new_password, PASSWORD_DEFAULT);
        }

        $stm = $this->connection->prepare(
            "update user set nom = ?, email = ?, password = ?, role = ? where id = ?"
        );
        return $stm->execute([$this->nom, $this->email, $this->password, $this->role, $this->userid]);
    }

    // show profile
    public function show_prof()
    {
        $stm = $this->connection->prepare("select * from user where id = ?");
        $stm->execute([$this->userid]);
        return $stm->fetch(PDO::FETCH_ASSOC);
    }

    // login
    public function login($email, $password)
    {
        $stm = $this->connection->prepare("select * from user where email = ?");
        $stm->execute([$email]);
        $user = $stm->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $this->userid = $user['id'];
            $this->nom = $user['nom'];
            $this->email = $user['email'];
            $this->role = $user['role'];
            return true;
        }

        return false;
    }

    // logout
    public function logout()
    {
        session_start();
        session_destroy();
        return true;
    }
}
?>
