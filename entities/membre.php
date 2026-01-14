<?php 
require_once './entities/user.php';

class membre extends user 
{
    private $title;
    private $description;
    private $status;
    private $date_fin;
    private $sprint_id;
    private $task_id;

    public function canEdit($task_user_id)
    {
        return $this->userid == $task_user_id;
    }

    public function canDelete($task_user_id)
    {
        return $this->userid == $task_user_id;
    }

    public function set_title($title) { $this->title = $title; }
    public function set_description($description) { $this->description = $description; }
    public function set_status($status) { $this->status = $status; }
    public function set_date_fin($date_fin) { $this->date_fin = $date_fin; }
    public function set_sprint_id($sprint_id) { $this->sprint_id = $sprint_id; }
    public function set_task_id($task_id) { $this->task_id = $task_id; }

    public function get_title() { return $this->title; }
    public function get_description() { return $this->description; }
    public function get_status() { return $this->status; }
    public function get_date_fin() { return $this->date_fin; }
    public function get_sprint_id() { return $this->sprint_id; }
    public function get_task_id() { return $this->task_id; }

    public function createtask()
    {
        $stm = $this->connection->prepare(
            "INSERT INTO task (title, description, status, date_fin, sprint_id, user_id) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stm->execute([$this->title, $this->description, $this->status, $this->date_fin, $this->sprint_id, $this->userid]);
        $this->task_id = $this->connection->lastInsertId();
        return $this->task_id;
    }

    public function update_task()
    {
        $stm = $this->connection->prepare(
            "UPDATE task SET title = ?, description = ?, status = ?, date_fin = ?, sprint_id = ? WHERE task_id = ?"
        );
        $stm->execute([$this->title, $this->description, $this->status, $this->date_fin, $this->sprint_id, $this->task_id]);
    }

    public function delete_task()
    {
        $stm = $this->connection->prepare("DELETE FROM task WHERE task_id = ?");
        $stm->execute([$this->task_id]);
    }

    public function make_as_done()
    {
        $stm = $this->connection->prepare("UPDATE task SET status = 'terminee' WHERE task_id = ?");
        $stm->execute([$this->task_id]);
    }
}
?>