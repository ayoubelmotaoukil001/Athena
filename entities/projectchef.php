<?php
require_once './entities/user.php';

class projectchef extends user
{
    public function canEdit($task_user_id)
    {
        return true;
    }

    public function canDelete($task_user_id)
    {
        return true;
    }

    public function create_project($titre, $description, $etat = 'actif')
    {
        $stm = $this->connection->prepare(
            "INSERT INTO project (titre, description, etat, chef_id) VALUES (?, ?, ?, ?)"
        );
        $stm->execute([$titre, $description, $etat, $this->userid]);
        return $this->connection->lastInsertId();
    }

    public function create_sprint($nom, $date_debut, $date_fin, $projet_id)
    {
        $stm = $this->connection->prepare(
            "INSERT INTO sprint (nom, date_debut, date_fin, projet_id) VALUES (?, ?, ?, ?)"
        );
        return $stm->execute([$nom, $date_debut, $date_fin, $projet_id]);
    }

    public function assign_task($title, $description, $status, $date_fin, $sprint_id, $user_id)
    {
        $stm = $this->connection->prepare(
            "INSERT INTO task (title, description, status, date_fin, sprint_id, user_id) VALUES (?, ?, ?, ?, ?, ?)"
        );
        return $stm->execute([$title, $description, $status, $date_fin, $sprint_id, $user_id]);
    }
}
?>