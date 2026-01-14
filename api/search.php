<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../repositories/Project.php';
require_once __DIR__ . '/../repositories/Sprint.php';
require_once __DIR__ . '/../repositories/Task.php';

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'projects':
            $projectRepo = new Project();
            $projects = $projectRepo->getAll();
            echo json_encode(['success' => true, 'data' => $projects]);
            break;

        case 'sprints':
            $sprintRepo = new Sprint();
            if (isset($_GET['project_id'])) {
                $sprints = $sprintRepo->getByProject($_GET['project_id']);
            } else {
                $sprints = $sprintRepo->getAll();
            }
            echo json_encode(['success' => true, 'data' => $sprints]);
            break;

        case 'tasks':
            $taskRepo = new Task();
            if (isset($_GET['sprint_id'])) {
                $tasks = $taskRepo->getBySprint($_GET['sprint_id']);
            } elseif (isset($_GET['user_id'])) {
                $tasks = $taskRepo->getByUser($_GET['user_id']);
            } else {
                $tasks = $taskRepo->getAll();
            }
            echo json_encode(['success' => true, 'data' => $tasks]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Action invalide']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
