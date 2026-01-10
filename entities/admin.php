<?php
require_once './entities/user.php' ;

class admin extends user
{
  public function showstatistique()
  {
    $stats = [] ;
    $stats['user'] = $this->connection
    ->query("select role, count(*) as total from user group by role ") 
    -> fetchAll(PDO::FETCH_ASSOC);

    $stats['projects'] = $this->connection
    ->query("select etat , count(*) as total from project group by etat")
    -> fetchall(PDO::FETCH_ASSOC);

    $stats['sprint'] = $this->connection
    ->query("select projet_id ,count(*) as total from sprint group by projet_id")
    ->fetchAll(PDO::FETCH_ASSOC) ;

    $stats ['tasks'] = $this->connection
    -> query("select status , count(*) as total from task group by  status ")
    ->fetchAll(PDO::FETCH_ASSOC);
    return  $stats;
    }
    //manageusers
public function adduser() 
{
 $stm = $this->connection-> prepare("insert into user  (nom , email ,password ,role) values(?,?,?,?)");
  $stm -> execute([$this->nom , $this->email ,$this->password ,$this->role]) ;
$this->userid = $this->connection->lastInsertId();
return $this->userid;
}
 public function updateuser()
 {
    $stm = $this-> connection-> prepare("update user set nom = ?  , email = ?  , password = ? , role = ? where id = ?");
        $stm->execute([$this->nom  , $this->email   , $this->password , $this->role  , $this->userid]) ;
 }
 public function deleteuser()
 {
    $stm = $this-> connection-> prepare("delete from user  where id = ?");
        $stm->execute([ $this->userid]) ;
 }


 //manage rules
public function getRoles() 
{
    return ['admin','projectchef','membre'];
}

public function setrol($userId, $newRole)
{
    $stm = $this->connection->prepare("update user set role=? where id=?");
    $stm->execute([$newRole, $userId]);
}
  public function desactivateProject($projectId){
    $stm = $this->connection->prepare("update project set etat = \" inactif\" where projet_id=?");
    $stm->execute([$this->$projectId]) ;
  }


}

?>