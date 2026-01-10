<?php
require_once __DIR__ . '/../config/db.php';


class project {
    private $projet_id;
    private $titre;
    private $descreption; 
    private $statut;
    private $chef_proj;

    private $connection;

    public function __construct()
    {
        $this->connection = db::getInstance()->getConnection();
    }

    

    // setters
    public function settitle($titre)
    {
        $this->titre = $titre;
    }

    public function setdescreption($descreption)
    {
        $this->descreption = $descreption;
    }

    public function setstaut($statut)
    {
        $this->statut = $statut;
    }

    public function setchef_id($chef_proj)
    {
        $this->chef_proj = $chef_proj;
    }

    // getters 
    public function gettitre()
    {
        return $this->titre;
    }

    public function getdescreption()
    {
        return $this->descreption;
    }

    public function getstatut()
    {
        return $this->statut;
    }

    public function getchef_id()
    {
        return $this->chef_proj;
    }


}
?>
