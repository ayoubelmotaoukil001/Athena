<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../repositories/Task.php';
require_once __DIR__ . '/../core/Exceptions.php';

$method = $_SERVER['REQUEST_METHOD'];
$taskRepo = new Task();

try {
    switch ($method) {
        case 'GET':
            if (isset($_GET['id'])) {
                $task = $taskRepo->show($_GET['id']);
                echo json_encode(['success' => true, 'data' => $task]);
            } elseif (isset($_GET['sprint_id'])) {
                $tasks = $taskRepo->getBySprint($_GET['sprint_id']);
                echo json_encode(['success' => true, 'data' => $tasks]);
            } elseif (isset($_GET['user_id'])) {
                $tasks = $taskRepo->getByUser($_GET['user_id']);
                echo json_encode(['success' => true, 'data' => $tasks]);
            } else {
                $tasks = $taskRepo->getAll();
                echo json_encode(['success' => true, 'data' => $tasks]);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                throw new Exception('Invalid JSON data');
            }
            
            $taskId = $taskRepo->create(
                $data['title'],
                $data['description'] ?? '',
                $data['status'],
                $data['date_fin'] ?? null,
                $data['sprint_id'],
                $data['user_id']
            );
            
            echo json_encode([
                'success' => true,
                'message' => 'Tâche créée avec succès',
                'task_id' => $taskId
            ]);
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            
            $taskRepo->update(
                $data['task_id'],
                $data['title'],
                $data['description'] ?? '',
                $data['status'],
                $data['date_fin'] ?? null,
                $data['sprint_id'],
                $data['user_id']
            );
            
            echo json_encode([
                'success' => true,
                'message' => 'Tâche mise à jour avec succès'
            ]);
            break;

        case 'DELETE':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['task_id'])) {
                throw new Exception('Task ID required');
            }
            
            $taskRepo->delete($data['task_id']);
            
            echo json_encode([
                'success' => true,
                'message' => 'Tâche supprimée avec succès'
            ]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    }
    
} catch (DuplicateException $e) {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (NotFoundException $e) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
