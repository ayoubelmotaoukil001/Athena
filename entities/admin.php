<?php
require_once './entities/user.php';

class admin extends user
{
    // Polymorphism: Admin can edit ANY task
    public function canEdit($task_user_id)
    {
        return true; // Admin can edit everything
    }

    // Polymorphism: Admin can delete ANY task
    public function canDelete($task_user_id)
    {
        return true; // Admin can delete everything
    }

    public function showstatistique()
    {
        $stats = [];

        $stats['user'] = $this->connection
            ->query("SELECT role, COUNT(*) AS total FROM user GROUP BY role")
            ->fetchAll(PDO::FETCH_ASSOC);

        $stats['projects'] = $this->connection
            ->query("SELECT etat, COUNT(*) AS total FROM project GROUP BY etat")
            ->fetchAll(PDO::FETCH_ASSOC);

        $stats['sprint'] = $this->connection
            ->query("SELECT COUNT(*) AS total FROM sprint")
            ->fetch(PDO::FETCH_ASSOC);

        $stats['tasks'] = $this->connection
            ->query("SELECT status, COUNT(*) AS total FROM task GROUP BY status")
            ->fetchAll(PDO::FETCH_ASSOC);

        return $stats;
    }

    public function adduser($nom, $email, $password, $role)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stm = $this->connection->prepare(
            "INSERT INTO user (nom, email, password, role, is_active) VALUES (?, ?, ?, ?, 1)"
        );
        $stm->execute([$nom, $email, $hashedPassword, $role]);
        return $this->connection->lastInsertId();
    }

    public function updateuser($userid, $nom, $email, $password, $role, $is_active = 1)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stm = $this->connection->prepare(
            "UPDATE user SET nom = ?, email = ?, password = ?, role = ?, is_active = ? WHERE id = ?"
        );
        $stm->execute([$nom, $email, $hashedPassword, $role, $is_active, $userid]);
    }

    public function deleteuser($userid)
    {
        $stm = $this->connection->prepare("DELETE FROM user WHERE id = ?");
        $stm->execute([$userid]);
    }

    public function getRoles()
    {
        return ['admin', 'projectchef', 'membre'];
    }

    public function setrol($userId, $newRole)
    {
        $stm = $this->connection->prepare("UPDATE user SET role = ? WHERE id = ?");
        $stm->execute([$newRole, $userId]);
    }

    public function desactivateUser($userId)
    {
        $stm = $this->connection->prepare("UPDATE user SET is_active = 0 WHERE id = ?");
        $stm->execute([$userId]);
    }

    public function activateUser($userId)
    {
        $stm = $this->connection->prepare("UPDATE user SET is_active = 1 WHERE id = ?");
        $stm->execute([$userId]);
    }

    public function desactivateProject($projectId)
    {
        $stm = $this->connection->prepare("UPDATE project SET etat = 'inactif' WHERE projet_id = ?");
        $stm->execute([$projectId]);
    }

    public function activateProject($projectId)
    {
        $stm = $this->connection->prepare("UPDATE project SET etat = 'actif' WHERE projet_id = ?");
        $stm->execute([$projectId]);
    }
}
?>