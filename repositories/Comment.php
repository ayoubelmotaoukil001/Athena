<?php
require_once __DIR__ . '/../config/db.php';

class Comment
{
    private $connection;

    public function __construct()
    {
        $this->connection = db::getInstance()->getConnection();
    }

    // CREATE
    public function create($contenu, $user_id, $task_id)
    {
        $stm = $this->connection->prepare(
            "INSERT INTO comment (contenu, user_id, task_id) VALUES (?, ?, ?)"
        );
        $stm->execute([$contenu, $user_id, $task_id]);
        return $this->connection->lastInsertId();
    }

    // UPDATE
    public function update($comment_id, $contenu)
    {
        $stm = $this->connection->prepare("UPDATE comment SET contenu = ? WHERE id = ?");
        return $stm->execute([$contenu, $comment_id]);
    }

    // SHOW
    public function show($comment_id)
    {
        $stm = $this->connection->prepare("SELECT * FROM comment WHERE id = ?");
        $stm->execute([$comment_id]);
        return $stm->fetch(PDO::FETCH_ASSOC);
    }

    // DELETE
    public function delete($comment_id)
    {
        $stm = $this->connection->prepare("DELETE FROM comment WHERE id = ?");
        return $stm->execute([$comment_id]);
    }

    // GET BY TASK
    public function getByTask($task_id)
    {
        $stm = $this->connection->prepare(
            "SELECT c.*, u.nom as user_nom 
             FROM comment c 
             LEFT JOIN user u ON c.user_id = u.id 
             WHERE c.task_id = ? 
             ORDER BY c.id DESC"
        );
        $stm->execute([$task_id]);
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>