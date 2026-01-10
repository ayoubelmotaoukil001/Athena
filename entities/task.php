<?php
class task 
 {
    private $taskid ;
    private $title;
    private $descreption ;
    private $statut ;
    private $datefin ;
    private $sprintid ;
    private $userid ;

      private $connection ;
    public function __construct()
    {
        $this->connection = db::getInstance()->getConnection();
    }

    //setters
    public function settitile($title)
    {
      $this->title=$title;
    }
    public function setdescreption($descreption)
    {
      $this->descreption =$descreption;
    }
    public function setstaut($statut)
    {
      $this->statut=$statut;
    }
    public function setdatefin($datefin)
    {
      $this->datefin=$datefin;
    }
    public function setsprintid($sprintid)
    {
      $this->sprintid=$sprintid;
    }
    public function setuserid($userid)
    {
      $this->userid =$userid;
    }
    //getter

    public function gettitle()
    {
      return $this->title;
    }

    public function getdescreption()
    {
      return $this->descreption;
    }
   
    public function getstatut() 
     {
      return $this->statut;
    }
    public function getdatefin()
    {
      return $this->datefin;
    }
    public function getsprintid()
    {
      return $this->sprintid;
    }
    public function getuserid()
    {
      return $this->userid;
    }

    


 }
 
 ?>
 