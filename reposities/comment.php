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
            "INSERT INTO comment (contenu, user_id, task_id)
             VALUES (?, ?, ?)"
        );
        $stm->execute([$contenu, $user_id, $task_id]);

        return $this->connection->lastInsertId();
    }

    // UPDATE
    public function update($comment_id, $contenu, $user_id, $task_id)
    {
        $stm = $this->connection->prepare(
            "UPDATE comment
             SET contenu = ?, user_id = ?, task_id = ?
             WHERE id = ?"
        );

        return $stm->execute([
            $contenu,
            $user_id,
            $task_id,
            $comment_id
        ]);
    }

    // SHOW
    public function show($comment_id)
    {
        $stm = $this->connection->prepare(
            "SELECT * FROM comment WHERE id = ?"
        );
        $stm->execute([$comment_id]);

        return $stm->fetch(PDO::FETCH_ASSOC);
    }

    // DELETE
    public function delete($comment_id)
    {
        $stm = $this->connection->prepare(
            "DELETE FROM comment WHERE id = ?"
        );

        return $stm->execute([$comment_id]);
    }
}
