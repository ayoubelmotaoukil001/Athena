<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../core/Exceptions.php';

class Sprint
{
    private $connection;

    public function __construct()
    {
        $this->connection = db::getInstance()->getConnection();
    }

    // CREATE
    public function create($nom, $date_debut, $date_fin, $projet_id)
    {
        // Check date conflict
        if ($this->checkDateConflict($date_debut, $date_fin, $projet_id)) {
            throw new SprintConflictException("Conflit de dates avec un autre sprint");
        }

        $stm = $this->connection->prepare(
            "INSERT INTO sprint (nom, date_debut, date_fin, projet_id) VALUES (?, ?, ?, ?)"
        );
        $stm->execute([$nom, $date_debut, $date_fin, $projet_id]);
        return $this->connection->lastInsertId();
    }

    // UPDATE
    public function update($sprint_id, $nom, $date_debut, $date_fin, $projet_id)
    {
        // Check date conflict (excluding current sprint)
        if ($this->checkDateConflict($date_debut, $date_fin, $projet_id, $sprint_id)) {
            throw new SprintConflictException("Conflit de dates avec un autre sprint");
        }

        $stm = $this->connection->prepare(
            "UPDATE sprint SET nom = ?, date_debut = ?, date_fin = ?, projet_id = ? WHERE sprintid = ?"
        );
        return $stm->execute([$nom, $date_debut, $date_fin, $projet_id, $sprint_id]);
    }

    // SHOW
    public function show($sprint_id)
    {
        $stm = $this->connection->prepare("SELECT * FROM sprint WHERE sprintid = ?");
        $stm->execute([$sprint_id]);
        $sprint = $stm->fetch(PDO::FETCH_ASSOC);
        
        if (!$sprint) {
            throw new NotFoundException("Sprint non trouvé");
        }
        
        return $sprint;
    }

    // DELETE
    public function delete($sprint_id)
    {
        $stm = $this->connection->prepare("DELETE FROM sprint WHERE sprintid = ?");
        return $stm->execute([$sprint_id]);
    }

    // GET ALL
    public function getAll()
    {
        $stm = $this->connection->query("SELECT * FROM sprint ORDER BY sprintid DESC");
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    // GET BY PROJECT
    public function getByProject($projet_id)
    {
        $stm = $this->connection->prepare("SELECT * FROM sprint WHERE projet_id = ? ORDER BY date_debut DESC");
        $stm->execute([$projet_id]);
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    // CHECK DATE CONFLICT
    public function checkDateConflict($date_debut, $date_fin, $projet_id, $exclude_sprint_id = null)
    {
        $sql = "SELECT sprintid FROM sprint 
                WHERE projet_id = ? 
                AND (
                    (date_debut BETWEEN ? AND ?) OR
                    (date_fin BETWEEN ? AND ?) OR
                    (? BETWEEN date_debut AND date_fin) OR
                    (? BETWEEN date_debut AND date_fin)
                )";
        
        $params = [$projet_id, $date_debut, $date_fin, $date_debut, $date_fin, $date_debut, $date_fin];

        if ($exclude_sprint_id) {
            $sql .= " AND sprintid != ?";
            $params[] = $exclude_sprint_id;
        }

        $stm = $this->connection->prepare($sql);
        $stm->execute($params);
        return $stm->fetch(PDO::FETCH_ASSOC) !== false;
    }
}
?>