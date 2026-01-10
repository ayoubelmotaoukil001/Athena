<?php
require_once __DIR__ . '/config/db.php';

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
        $stm = $this->connection->prepare(
            "INSERT INTO sprint (nom, date_debut, date_fin, projet_id)
             VALUES (?, ?, ?, ?)"
        );
        $stm->execute([$nom, $date_debut, $date_fin, $projet_id]);

        return $this->connection->lastInsertId();
    }

    // UPDATE
    public function update($sprint_id, $nom, $date_debut, $date_fin, $projet_id)
    {
        $stm = $this->connection->prepare(
            "UPDATE sprint
             SET nom = ?, date_debut = ?, date_fin = ?, projet_id = ?
             WHERE sprintid = ?"
        );

        return $stm->execute([
            $nom,
            $date_debut,
            $date_fin,
            $projet_id,
            $sprint_id
        ]);
    }

    // SHOW
    public function show($sprint_id)
    {
        $stm = $this->connection->prepare(
            "SELECT * FROM sprint WHERE sprintid = ?"
        );
        $stm->execute([$sprint_id]);

        return $stm->fetch(PDO::FETCH_ASSOC);
    }

    // DELETE
    public function delete($sprint_id)
    {
        $stm = $this->connection->prepare(
            "DELETE FROM sprint WHERE sprintid = ?"
        );

        return $stm->execute([$sprint_id]);
    }
}
