<?php
require_once __DIR__ . '/config/db.php';

class repNotification
{
    private $connection;

    public function __construct()
    {
        $this->connection = db::getInstance()->getConnection();
    }

    // CREATE
    public function create($message, $user_id, $task_id)
    {
        $stm = $this->connection->prepare(
            "INSERT INTO notification (message, user_id, task_id)
             VALUES (?, ?, ?)"
        );
        $stm->execute([$message, $user_id, $task_id]);

        return $this->connection->lastInsertId();
    }

    // UPDATE
    public function update($notif_id, $message, $user_id, $task_id)
    {
        $stm = $this->connection->prepare(
            "UPDATE notification
             SET message = ?, user_id = ?, task_id = ?
             WHERE id = ?"
        );

        return $stm->execute([
            $message,
            $user_id,
            $task_id,
            $notif_id
        ]);
    }

    // SHOW
    public function show($notif_id)
    {
        $stm = $this->connection->prepare(
            "SELECT * FROM notification WHERE id = ?"
        );
        $stm->execute([$notif_id]);

        return $stm->fetch(PDO::FETCH_ASSOC);
    }

    // DELETE
    public function delete($notif_id)
    {
        $stm = $this->connection->prepare(
            "DELETE FROM notification WHERE id = ?"
        );

        return $stm->execute([$notif_id]);
    }
}
