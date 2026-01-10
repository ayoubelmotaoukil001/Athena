<?php
 class comment
 {
    private $commentid;
    private $contenu;
    private $userid ;
    private $taskid ;
    private $connection ;
    public function __construct()
    {
        $this->connection = db::getInstance()->getConnection();
    }

    public function setcontenu ($contenu)
    {
      $this->contenu =$contenu;
    }
    public function setuserid($userid)
    {
      $this->userid = $userid;
    }
    public function settaskid ($taskid)
    {
      $this->taskid =$taskid;
    }

    public function getcontenu()
    {
      return $this->contenu;
    }
    public function getuserid()
    {
      return $this->userid;
    }
    public function gettaskid()
    {
      return $this->taskid;
    }

   
 }
 ?>
