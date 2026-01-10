<?php
 class notification
 {
    private $notifid ;
    private $message;
    private $userid;
    private $taskid ;

    private $connection ;
    public function __construct()
    {
        $this->connection = db::getInstance()->getConnection();
    }

    public function setmessage($message)
    {
      $this->message=$message;
    }
    public function setuserid($userid)
    {
      $this->userid = $userid;
    }
    public function settaskid($taskid)
    {
      $this->taskid =$taskid;
    }



    public function getmessage()
    {
      return $this->message;
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
 