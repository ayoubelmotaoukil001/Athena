<?php
require_once __DIR__ . '/../config/db.php';

class project
{
    private $projet_id;
    private $titre;
    private $description;
    private $statut;
    private $chef_proj;

    private $connection;

    public function __construct()
    {
        $this->connection = db::getInstance()->getConnection();
    }

    public function set_projet_id($projet_id)
    {
        $this->projet_id = $projet_id;
    }

    public function set_titre($titre)
    {
        $this->titre = $titre;
    }

    public function set_description($description)
    {
        $this->description = $description;
    }

    public function set_statut($statut)
    {
        $this->statut = $statut;
    }

    public function set_chef_id($chef_proj)
    {
        $this->chef_proj = $chef_proj;
    }

    public function get_projet_id()
    {
        return $this->projet_id;
    }

    public function get_titre()
    {
        return $this->titre;
    }

    public function get_description()
    {
        return $this->description;
    }

    public function get_statut()
    {
        return $this->statut;
    }

    public function get_chef_id()
    {
        return $this->chef_proj;
    }
}
?>
