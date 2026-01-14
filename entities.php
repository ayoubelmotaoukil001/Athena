// ======================= core/Exceptions.php =======================
<?php
class DatabaseException extends Exception {}
class ValidationException extends Exception {}
class AuthenticationException extends Exception {}
class AuthorizationException extends Exception {}
class TaskConflictException extends Exception {}
class SprintConflictException extends Exception {}
class NotFoundException extends Exception {}
class DuplicateException extends Exception {}
?>

// ======================= utils/Validator.php =======================


// ======================= services/EmailService.php =======================
<?php
require_once __DIR__ . '/../config/db.php';

class EmailService
{
    private static function sendEmail($to, $subject, $message)
    {
        // Configuration email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: noreply@athena.com" . "\r\n";

        // Envoyer email (mail() ou SMTP)
        return mail($to, $subject, $message, $headers);
    }

    public static function sendTaskCreatedNotification($userEmail, $taskTitle)
    {
        $subject = "Nouvelle tâche assignée";
        $message = "
            <h2>Nouvelle tâche</h2>
            <p>Une nouvelle tâche vous a été assignée : <strong>$taskTitle</strong></p>
        ";
        return self::sendEmail($userEmail, $subject, $message);
    }

    public static function sendTaskStatusChanged($userEmail, $taskTitle, $newStatus)
    {
        $subject = "Statut de tâche modifié";
        $message = "
            <h2>Statut modifié</h2>
            <p>Le statut de la tâche <strong>$taskTitle</strong> a été changé à : <strong>$newStatus</strong></p>
        ";
        return self::sendEmail($userEmail, $subject, $message);
    }

    public static function sendCommentNotification($userEmail, $taskTitle, $commentAuthor)
    {
        $subject = "Nouveau commentaire";
        $message = "
            <h2>Nouveau commentaire</h2>
            <p><strong>$commentAuthor</strong> a commenté la tâche : <strong>$taskTitle</strong></p>
        ";
        return self::sendEmail($userEmail, $subject, $message);
    }
}
?>

// ======================= services/SearchService.php =======================
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

// ======================= repositories/Task.php - COMPLETE =======================
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

// ======================= repositories/Project.php - COMPLETE =======================
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

// ======================= repositories/Sprint.php - COMPLETE =======================
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

// ======================= repositories/Comment.php - COMPLETE =======================
<?php
require_once __DIR__ . '/../config/db.php';

class Comment
{
    private $connection;

    public function __construct()
    {
        $this->connection = db::getInstance()->getConnection();
    }

    // CREATE
    public function create($contenu, $user_id, $task_id)
    {
        $stm = $this->connection->prepare(
            "INSERT INTO comment (contenu, user_id, task_id) VALUES (?, ?, ?)"
        );
        $stm->execute([$contenu, $user_id, $task_id]);
        return $this->connection->lastInsertId();
    }

    // UPDATE
    public function update($comment_id, $contenu)
    {
        $stm = $this->connection->prepare("UPDATE comment SET contenu = ? WHERE id = ?");
        return $stm->execute([$contenu, $comment_id]);
    }

    // SHOW
    public function show($comment_id)
    {
        $stm = $this->connection->prepare("SELECT * FROM comment WHERE id = ?");
        $stm->execute([$comment_id]);
        return $stm->fetch(PDO::FETCH_ASSOC);
    }

    // DELETE
    public function delete($comment_id)
    {
        $stm = $this->connection->prepare("DELETE FROM comment WHERE id = ?");
        return $stm->execute([$comment_id]);
    }

    // GET BY TASK
    public function getByTask($task_id)
    {
        $stm = $this->connection->prepare(
            "SELECT c.*, u.nom as user_nom 
             FROM comment c 
             LEFT JOIN user u ON c.user_id = u.id 
             WHERE c.task_id = ? 
             ORDER BY c.id DESC"
        );
        $stm->execute([$task_id]);
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

// ======================= repositories/Notification.php - COMPLETE =======================
<?php
require_once __DIR__ . '/../config/db.php';

class Notification
{
    private $connection;

    public function __construct()
    {
        $this->connection = db::getInstance()->getConnection();
    }

    // CREATE
    public function create($message, $user_id, $task_id)
    {
        $stm = $this->connection->prepare(
            "INSERT INTO notification (message, user_id, task_id) VALUES (?, ?, ?)"
        );
        $stm->execute([$message, $user_id, $task_id]);
        return $this->connection->lastInsertId();
    }

    // SHOW
    public function show($notif_id)
    {
        $stm = $this->connection->prepare("SELECT * FROM notification WHERE id = ?");
        $stm->execute([$notif_id]);
        return $stm->fetch(PDO::FETCH_ASSOC);
    }

    // DELETE
    public function delete($notif_id)
    {
        $stm = $this->connection->prepare("DELETE FROM notification WHERE id = ?");
        return $stm->execute([$notif_id]);
    }

    // GET BY USER
    public function getByUser($user_id)
    {
        $stm = $this->connection->prepare(
            "SELECT n.*, t.title as task_title 
             FROM notification n 
             LEFT JOIN task t ON n.task_id = t.task_id 
             WHERE n.user_id = ? 
             ORDER BY n.id DESC"
        );
        $stm->execute([$user_id]);
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    // MARK AS READ (bonus method)
    public function markAsRead($notif_id)
    {
        $stm = $this->connection->prepare("UPDATE notification SET is_read = 1 WHERE id = ?");
        return $stm->execute([$notif_id]);
    }
}
?>

// ======================= entities/admin.php - WITH POLYMORPHISM =======================
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

// ======================= entities/membre.php =======================
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

// ======================= entities/projectchef.php =======================
<?php
require_once './entities/user.php';

class projectchef extends user
{
    public function canEdit($task_user_id)
    {
        return true;
    }

    public function canDelete($task_user_id)
    {
        return true;
    }

    public function create_project($titre, $description, $etat = 'actif')
    {
        $stm = $this->connection->prepare(
            "INSERT INTO project (titre, description, etat, chef_id) VALUES (?, ?, ?, ?)"
        );
        $stm->execute([$titre, $description, $etat, $this->userid]);
        return $this->connection->lastInsertId();
    }

    public function create_sprint($nom, $date_debut, $date_fin, $projet_id)
    {
        $stm = $this->connection->prepare(
            "INSERT INTO sprint (nom, date_debut, date_fin, projet_id) VALUES (?, ?, ?, ?)"
        );
        return $stm->execute([$nom, $date_debut, $date_fin, $projet_id]);
    }

    public function assign_task($title, $description, $status, $date_fin, $sprint_id, $user_id)
    {
        $stm = $this->connection->prepare(
            "INSERT INTO task (title, description, status, date_fin, sprint_id, user_id) VALUES (?, ?, ?, ?, ?, ?)"
        );
        return $stm->execute([$title, $description, $status, $date_fin, $sprint_id, $user_id]);
    }
}
?>

// ======================= database.sql - UPDATED =======================
CREATE DATABASE IF NOT EXISTS athena;
USE athena;

CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','projectchef','membre') NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE project (
    projet_id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(150) NOT NULL,
    description TEXT,
    etat ENUM('actif','inactif') DEFAULT 'actif',
    chef_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (chef_id) REFERENCES user(id) ON DELETE RESTRICT
);

CREATE TABLE sprint (
    sprintid INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    projet_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (projet_id) REFERENCES project(projet_id) ON DELETE CASCADE
);

CREATE TABLE task (
    task_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    status ENUM('a_faire','en_cours','terminee') DEFAULT 'a_faire',
    date_fin DATE,
    sprint_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sprint_id) REFERENCES sprint(sprintid) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE RESTRICT
);

CREATE TABLE comment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contenu TEXT NOT NULL,
    user_id INT NOT NULL,
    task_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY (task_id) REFERENCES task(task_id) ON DELETE CASCADE
);

CREATE TABLE notification (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message TEXT NOT NULL,
    user_id INT NOT NULL,
    task_id INT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY (task_id) REFERENCES task(task_id) ON DELETE CASCADE
);