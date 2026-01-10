<?php
require_once __DIR__.'/config/db.php' ;
 class sprint
 {
    private $sprintid ;
    private $nom ;
    private $datedebut ;
    private $datefin ;
    private $project_id ;

     private $connection ;
    public function __construct()
    {
        $this->connection = db::getInstance()->getConnection();
    }


    public function setname($nom)
    {
      $this->nom =$nom;
    }

    public function setprojetid($project_id) 
    {
       $this->project_id=$project_id; 
    }
     public function setdatedebut($datedebut)
    {
      $this-> datedebut =$datedebut;
    }
     public function setdatefin($datefin)
    {
      $this->datefin=$datefin;
    }

    public function getprojid()
    {
      return $this->project_id;
    }

    public function  getnom()
    {
      return $this->nom;
    }

    public function  getdatedebut()
    {
      return $this->datedebut;
    }
    public function  getdatefin()
    {
      return $this->datefin;
    }


   
 }
 ?>
 