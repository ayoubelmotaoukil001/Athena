<?php
require_once __DIR__ . '/../config/db.php';

class notification
{
    private $notif_id;
    private $message;
    private $user_id;
    private $task_id;
    private $connection;

    public function __construct()
    {
        $this->connection = db::getInstance()->getConnection();
    }

    public function set_notif_id($notif_id)
    {
        $this->notif_id = $notif_id;
    }

    public function set_message($message)
    {
        $this->message = $message;
    }

    public function set_user_id($user_id)
    {
        $this->user_id = $user_id;
    }

    public function set_task_id($task_id)
    {
        $this->task_id = $task_id;
    }

    public function get_notif_id()
    {
        return $this->notif_id;
    }

    public function get_message()
    {
        return $this->message;
    }

    public function get_user_id()
    {
        return $this->user_id;
    }

    public function get_task_id()
    {
        return $this->task_id;
    }
}
?>
