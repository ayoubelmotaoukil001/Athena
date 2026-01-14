<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../core/Exceptions.php';

class Project
{
    private $connection;

    public function __construct()
    {
        $this->connection = db::getInstance()->getConnection();
    }

    // CREATE
    public function create($titre, $description, $etat, $chef_id)
    {
        $stm = $this->connection->prepare(
            "INSERT INTO project (titre, description, etat, chef_id) VALUES (?, ?, ?, ?)"
        );
        $stm->execute([$titre, $description, $etat, $chef_id]);
        return $this->connection->lastInsertId();
    }

    // UPDATE
    public function update($projet_id, $titre, $description, $etat, $chef_id)
    {
        $stm = $this->connection->prepare(
            "UPDATE project SET titre = ?, description = ?, etat = ?, chef_id = ? WHERE projet_id = ?"
        );
        return $stm->execute([$titre, $description, $etat, $chef_id, $projet_id]);
    }

    // SHOW
    public function show($projet_id)
    {
        $stm = $this->connection->prepare("SELECT * FROM project WHERE projet_id = ?");
        $stm->execute([$projet_id]);
        $project = $stm->fetch(PDO::FETCH_ASSOC);
        
        if (!$project) {
            throw new NotFoundException("Projet non trouvé");
        }
        
        return $project;
    }

    // DELETE
    public function delete($projet_id)
    {
        $stm = $this->connection->prepare("DELETE FROM project WHERE projet_id = ?");
        return $stm->execute([$projet_id]);
    }

    // GET ALL
    public function getAll()
    {
        $stm = $this->connection->query("SELECT * FROM project ORDER BY projet_id DESC");
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    // GET BY CHEF
    public function getByChef($chef_id)
    {
        $stm = $this->connection->prepare("SELECT * FROM project WHERE chef_id = ? ORDER BY projet_id DESC");
        $stm->execute([$chef_id]);
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    // SEARCH
    public function search($filters = [])
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

        $sql .= " ORDER BY p.projet_id DESC";

        $stm = $this->connection->prepare($sql);
        $stm->execute($params);
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    // PAGINATE
    public function paginate($page = 1, $perPage = 10)
    {
        $offset = ($page - 1) * $perPage;

        // Count total
        $stmCount = $this->connection->query("SELECT COUNT(*) as total FROM project");
        $total = $stmCount->fetch(PDO::FETCH_ASSOC)['total'];

        // Get data
        $stm = $this->connection->prepare(
            "SELECT * FROM project ORDER BY projet_id DESC LIMIT ? OFFSET ?"
        );
        $stm->execute([$perPage, $offset]);
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
