<?php
require_once __DIR__ . '/../config/db.php';

class task 
{
    private $task_id;
    private $title;
    private $description;
    private $status;
    private $date_fin;
    private $sprint_id;
    private $user_id;

    private $connection;

    public function __construct()
    {
        $this->connection = db::getInstance()->getConnection();
    }

    public function set_task_id($task_id)
    {
        $this->task_id = $task_id;
    }

    public function set_title($title)
    {
        $this->title = $title;
    }

    public function set_description($description)
    {
        $this->description = $description;
    }

    public function set_status($status)
    {
        $this->status = $status;
    }

    public function set_date_fin($date_fin)
    {
        $this->date_fin = $date_fin;
    }

    public function set_sprint_id($sprint_id)
    {
        $this->sprint_id = $sprint_id;
    }

    public function set_user_id($user_id)
    {
        $this->user_id = $user_id;
    }

    public function get_task_id()
    {
        return $this->task_id;
    }

    public function get_title()
    {
        return $this->title;
    }

    public function get_description()
    {
        return $this->description;
    }

    public function get_status()
    {
        return $this->status;
    }

    public function get_date_fin()
    {
        return $this->date_fin;
    }

    public function get_sprint_id()
    {
        return $this->sprint_id;
    }

    public function get_user_id()
    {
        return $this->user_id;
    }
}
?>
