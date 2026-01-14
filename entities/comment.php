<?php
require_once __DIR__ . '/../config/db.php';

class comment
{
    private $comment_id;
    private $contenu;
    private $user_id;
    private $task_id;
    private $connection;

    public function __construct()
    {
        $this->connection = db::getInstance()->getConnection();
    }

    public function set_comment_id($comment_id)
    {
        $this->comment_id = $comment_id;
    }

    public function set_contenu($contenu)
    {
        $this->contenu = $contenu;
    }

    public function set_user_id($user_id)
    {
        $this->user_id = $user_id;
    }

    public function set_task_id($task_id)
    {
        $this->task_id = $task_id;
    }

    public function get_comment_id()
    {
        return $this->comment_id;
    }

    public function get_contenu()
    {
        return $this->contenu;
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
