<?php
require_once __DIR__ . '/../config/db.php';

class Notification
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
            "INSERT INTO notification (message, user_id, task_id) VALUES (?, ?, ?)"
        );
        $stm->execute([$message, $user_id, $task_id]);
        return $this->connection->lastInsertId();
    }

    // SHOW
    public function show($notif_id)
    {
        $stm = $this->connection->prepare("SELECT * FROM notification WHERE id = ?");
        $stm->execute([$notif_id]);
        return $stm->fetch(PDO::FETCH_ASSOC);
    }

    // DELETE
    public function delete($notif_id)
    {
        $stm = $this->connection->prepare("DELETE FROM notification WHERE id = ?");
        return $stm->execute([$notif_id]);
    }

    // GET BY USER
    public function getByUser($user_id)
    {
        $stm = $this->connection->prepare(
            "SELECT n.*, t.title as task_title 
             FROM notification n 
             LEFT JOIN task t ON n.task_id = t.task_id 
             WHERE n.user_id = ? 
             ORDER BY n.id DESC"
        );
        $stm->execute([$user_id]);
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    // MARK AS READ (bonus method)
    public function markAsRead($notif_id)
    {
        $stm = $this->connection->prepare("UPDATE notification SET is_read = 1 WHERE id = ?");
        return $stm->execute([$notif_id]);
    }
}
?>