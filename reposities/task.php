<?php
require_once __DIR__ . '/config/db.php';

class Task
{
    private $connection;

    public function __construct()
    {
        $this->connection = db::getInstance()->getConnection();
    }

 
    public function create(
        $title,
        $description,
        $status,
        $date_fin,
        $sprint_id,
        $user_id
    ) {
        $stm = $this->connection->prepare(
            "INSERT INTO task (title, description, status, date_fin, sprint_id, user_id)
             VALUES (?, ?, ?, ?, ?, ?)"
        );

        $stm->execute([
            $title,
            $description,
            $status,
            $date_fin,
            $sprint_id,
            $user_id
        ]);

        return $this->connection->lastInsertId();
    }

    
    public function update(
        $task_id,
        $title,
        $description,
        $status,
        $date_fin,
        $sprint_id,
        $user_id
    ) {
        $stm = $this->connection->prepare(
            "UPDATE task
             SET title = ?, description = ?, status = ?, date_fin = ?, sprint_id = ?, user_id = ?
             WHERE task_id = ?"
        );

        return $stm->execute([
            $title,
            $description,
            $status,
            $date_fin,
            $sprint_id,
            $user_id,
            $task_id
        ]);
    }


    public function show($task_id)
    {
        $stm = $this->connection->prepare(
            "SELECT * FROM task WHERE task_id = ?"
        );
        $stm->execute([$task_id]);

        return $stm->fetch(PDO::FETCH_ASSOC);
    }

    // DELETE
    public function delete($task_id)
    {
        $stm = $this->connection->prepare(
            "DELETE FROM task WHERE task_id = ?"
        );

        return $stm->execute([$task_id]);
    }
}
