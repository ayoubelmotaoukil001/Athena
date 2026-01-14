<?php
require_once __DIR__ . '/../config/db.php';

class sprint
{
    private $sprint_id;
    private $nom;
    private $date_debut;
    private $date_fin;
    private $projet_id;

    private $connection;

    public function __construct()
    {
        $this->connection = db::getInstance()->getConnection();
    }

    public function set_sprint_id($sprint_id)
    {
        $this->sprint_id = $sprint_id;
    }

    public function set_nom($nom)
    {
        $this->nom = $nom;
    }

    public function set_projet_id($projet_id)
    {
        $this->projet_id = $projet_id;
    }

    public function set_date_debut($date_debut)
    {
        $this->date_debut = $date_debut;
    }

    public function set_date_fin($date_fin)
    {
        $this->date_fin = $date_fin;
    }

    public function get_sprint_id()
    {
        return $this->sprint_id;
    }

    public function get_projet_id()
    {
        return $this->projet_id;
    }

    public function get_nom()
    {
        return $this->nom;
    }

    public function get_date_debut()
    {
        return $this->date_debut;
    }

    public function get_date_fin()
    {
        return $this->date_fin;
    }
}
?>
