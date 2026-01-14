<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

try {
    require_once __DIR__ . '/../config/db.php';
    require_once __DIR__ . '/../repositories/Task.php';
    require_once __DIR__ . '/../core/Exceptions.php';
    
    $method = $_SERVER['REQUEST_METHOD'];
    $taskRepo = new Task();
    
    switch ($method) {
        case 'GET':
            if (isset($_GET['search'])) {
                $filters = [
                    'title' => $_GET['title'] ?? '',
                    'status' => $_GET['status'] ?? ''
                ];
                $tasks = $taskRepo->search($filters);
                echo json_encode(['success' => true, 'data' => $tasks]);
            } else {
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $result = $taskRepo->paginate($page, 10);
                echo json_encode(['success' => true, 'data' => $result]);
            }
            break;
    
        case 'POST':
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
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
    
        case 'DELETE':
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (!$data || !isset($data['task_id'])) {
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