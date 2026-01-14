<?php
require_once __DIR__ . '/../config/db.php';

class SearchService
{
    private $connection;

    public function __construct()
    {
        $this->connection = db::getInstance()->getConnection();
    }

    public function searchTasks($filters = [], $page = 1, $perPage = 10)
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

        if (!empty($filters['sprint_id'])) {
            $sql .= " AND t.sprint_id = ?";
            $params[] = $filters['sprint_id'];
        }

        // Count total
        $countSql = "SELECT COUNT(*) as total FROM ($sql) as counted";
        $stmCount = $this->connection->prepare($countSql);
        $stmCount->execute($params);
        $total = $stmCount->fetch(PDO::FETCH_ASSOC)['total'];

        // Pagination
        $offset = ($page - 1) * $perPage;
        $sql .= " ORDER BY t.task_id DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;

        $stm = $this->connection->prepare($sql);
        $stm->execute($params);
        $tasks = $stm->fetchAll(PDO::FETCH_ASSOC);

        return [
            'data' => $tasks,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage)
        ];
    }

    public function searchProjects($filters = [], $page = 1, $perPage = 10)
    {
        $sql = "SELECT p.*, u.nom as chef_nom 
                FROM project p 
                LEFT JOIN user u ON p.chef_id = u.id 
                WHERE 1=1";
        
        $params = [];

        if (!empty($filters['titre'])) {
            $sql .= " AND p.titre LIKE ?";
            $params[] = "%" . $filters['titre'] . "%";
        }

        if (!empty($filters['etat'])) {
            $sql .= " AND p.etat = ?";
            $params[] = $filters['etat'];
        }

        // Count total
        $countSql = "SELECT COUNT(*) as total FROM ($sql) as counted";
        $stmCount = $this->connection->prepare($countSql);
        $stmCount->execute($params);
        $total = $stmCount->fetch(PDO::FETCH_ASSOC)['total'];

        // Pagination
        $offset = ($page - 1) * $perPage;
        $sql .= " ORDER BY p.projet_id DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;

        $stm = $this->connection->prepare($sql);
        $stm->execute($params);
        $projects = $stm->fetchAll(PDO::FETCH_ASSOC);

        return [
            'data' => $projects,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage)
        ];
    }
}
?>