<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../core/Exceptions.php';

class Task
{
    private $connection;

    public function __construct()
    {
        $this->connection = db::getInstance()->getConnection();
    }

    // CREATE
    public function create($title, $description, $status, $date_fin, $sprint_id, $user_id)
    {
        // Check duplicate
        if ($this->checkDuplicate($title, $sprint_id)) {
            throw new DuplicateException("Cette tâche existe déjà dans ce sprint");
        }

        $stm = $this->connection->prepare(
            "INSERT INTO task (title, description, status, date_fin, sprint_id, user_id) 
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stm->execute([$title, $description, $status, $date_fin, $sprint_id, $user_id]);
        return $this->connection->lastInsertId();
    }

    // UPDATE
    public function update($task_id, $title, $description, $status, $date_fin, $sprint_id, $user_id)
    {
        $stm = $this->connection->prepare(
            "UPDATE task 
             SET title = ?, description = ?, status = ?, date_fin = ?, sprint_id = ?, user_id = ? 
             WHERE task_id = ?"
        );
        return $stm->execute([$title, $description, $status, $date_fin, $sprint_id, $user_id, $task_id]);
    }

    // SHOW
    public function show($task_id)
    {
        $stm = $this->connection->prepare("SELECT * FROM task WHERE task_id = ?");
        $stm->execute([$task_id]);
        $task = $stm->fetch(PDO::FETCH_ASSOC);
        
        if (!$task) {
            throw new NotFoundException("Tâche non trouvée");
        }
        
        return $task;
    }

    // DELETE
    public function delete($task_id)
    {
        $stm = $this->connection->prepare("DELETE FROM task WHERE task_id = ?");
        return $stm->execute([$task_id]);
    }

    // GET ALL
    public function getAll()
    {
        $stm = $this->connection->query("SELECT * FROM task ORDER BY task_id DESC");
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    // GET BY USER
    public function getByUser($user_id)
    {
        $stm = $this->connection->prepare("SELECT * FROM task WHERE user_id = ? ORDER BY task_id DESC");
        $stm->execute([$user_id]);
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    // GET BY SPRINT
    public function getBySprint($sprint_id)
    {
        $stm = $this->connection->prepare("SELECT * FROM task WHERE sprint_id = ? ORDER BY task_id DESC");
        $stm->execute([$sprint_id]);
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    // CHECK DUPLICATE
    public function checkDuplicate($title, $sprint_id, $exclude_task_id = null)
    {
        $sql = "SELECT task_id FROM task WHERE title = ? AND sprint_id = ?";
        $params = [$title, $sprint_id];

        if ($exclude_task_id) {
            $sql .= " AND task_id != ?";
            $params[] = $exclude_task_id;
        }

        $stm = $this->connection->prepare($sql);
        $stm->execute($params);
        return $stm->fetch(PDO::FETCH_ASSOC) !== false;
    }

    // SEARCH
    public function search($filters = [])
    {
        $sql = "SELECT t.*, u.nom as user_nom, s.nom as sprint_nom 
                FROM task t 
                LEFT JOIN user u ON t.user_id = u.id 
                LEFT JOIN sprint s ON t.sprint_id = s.sprintid 
                WHERE 1=1";
        
        $params = [];

        if (!empty($filters['title'])) {
            $sql .= " AND t.title LIKE ?";
            $params[] = "%" . $filters['title'] . "%";
        }

        if (!empty($filters['status'])) {
            $sql .= " AND t.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['user_id'])) {
            $sql .= " AND t.user_id = ?";
            $params[] = $filters['user_id'];
        }

        $sql .= " ORDER BY t.task_id DESC";

        $stm = $this->connection->prepare($sql);
        $stm->execute($params);
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    // PAGINATE
    public function paginate($page = 1, $perPage = 10)
    {
        $offset = ($page - 1) * $perPage;

        // Count total
        $stmCount = $this->connection->query("SELECT COUNT(*) as total FROM task");
        $total = $stmCount->fetch(PDO::FETCH_ASSOC)['total'];

        // Get data
        $stm = $this->connection->prepare(
            "SELECT * FROM task ORDER BY task_id DESC LIMIT ? OFFSET ?"
        );
        $stm->execute([$perPage, $offset]);
        $tasks = $stm->fetchAll(PDO::FETCH_ASSOC);

        return [
            'data' => $tasks,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage)
        ];
    }
}
?>