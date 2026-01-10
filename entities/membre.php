<?php 
require_once './entities/user.php'  ;
class membre extends user 
{
    private $title;
    private $descreption;  
    private $statut;
    private $datefin;
    private $sprintid;
    private $taskid;

   public function createtask()
    {
      $stm = $this->connection->prepare("insert into task (title ,description  ,status , date_fin  , sprint_id ) values (?,?,?,?,?)");
      $stm-> execute([$this->title  , $this->descreption,$this->statut ,$this->datefin ,$this->sprintid]) ;
      $this->taskid=$this->connection->lastInsertId();
      return $this->taskid;
   }
   public function update_task()
   {
      $stm=$this->connection->prepare("update task set title = ? , description=?  , status = ? , date_fin = ? , sprint_id=?  where task_id= ?");
      $stm-> execute([$this->title  , $this-> descreption , $this->statut , $this->datefin  , $this->sprintid   , $this-> taskid]) ;
   }

   public function dalete_task()
   {
      $stm = $this->connection->prepare("delete from task where task_id =?");
      $stm->execute([$this->taskid]) ;
   }

    public  function make_as_done ()
   {
      $stm= $this->connection->prepare("update  task set status = \" terminee\" where task_id=?");
      $stm->execute([$this->taskid]) ;
      return $stm->fetch(PDO::FETCH_ASSOC) ;
   }
}
?>