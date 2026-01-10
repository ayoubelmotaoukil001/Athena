<?php
require_once __DIR__ . '/../config/db.php';

class repoProject
{
    private $connection;

    public function __construct()
    {
        $this->connection = db::getInstance()->getConnection();
    }

    // CREATE
    public function create($titre, $description, $statut, $chef_proj)
    {
        $stm = $this->connection->prepare(
            "INSERT INTO project (titre, description, etat, chef_id)
             VALUES (?, ?, ?, ?)"
        );
        $stm->execute([$titre, $description, $statut, $chef_proj]);

        return $this->connection->lastInsertId();
    }

    // UPDATE
    public function update($projet_id, $titre, $description, $statut, $chef_proj)
    {
        $stm = $this->connection->prepare(
            "UPDATE project
             SET titre = ?, description = ?, etat = ?, chef_id = ?
             WHERE projet_id = ?"
        );

        return $stm->execute([
            $titre,
            $description,
            $statut,
            $chef_proj,
            $projet_id
        ]);
    }

    // SHOW
    public function show($projet_id)
    {
        $stm = $this->connection->prepare(
            "SELECT * FROM project WHERE projet_id = ?"
        );
        $stm->execute([$projet_id]);

        return $stm->fetch(PDO::FETCH_ASSOC);
    }

    // DELETE
    public function delete($projet_id)
    {
        $stm = $this->connection->prepare(
            "DELETE FROM project WHERE projet_id = ?"
        );

        return $stm->execute([$projet_id]);
    }
}
