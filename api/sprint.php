<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../repositories/Sprint.php';
require_once __DIR__ . '/../core/Exceptions.php';

$method = $_SERVER['REQUEST_METHOD'];
$sprintRepo = new Sprint();

try {
    switch ($method) {
        case 'GET':
            if (isset($_GET['id'])) {
                $sprint = $sprintRepo->show($_GET['id']);
                echo json_encode(['success' => true, 'data' => $sprint]);
            } elseif (isset($_GET['project_id'])) {
                $sprints = $sprintRepo->getByProject($_GET['project_id']);
                echo json_encode(['success' => true, 'data' => $sprints]);
            } else {
                $sprints = $sprintRepo->getAll();
                echo json_encode(['success' => true, 'data' => $sprints]);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                throw new Exception('Invalid JSON data');
            }
            
            $sprintId = $sprintRepo->create(
                $data['nom'],
                $data['date_debut'],
                $data['date_fin'],
                $data['projet_id']
            );
            
            echo json_encode([
                'success' => true,
                'message' => 'Sprint créé avec succès',
                'sprint_id' => $sprintId
            ]);
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            
            $sprintRepo->update(
                $data['sprint_id'],
                $data['nom'],
                $data['date_debut'],
                $data['date_fin'],
                $data['projet_id']
            );
            
            echo json_encode([
                'success' => true,
                'message' => 'Sprint mis à jour avec succès'
            ]);
            break;

        case 'DELETE':
            $data = json_decode(file_get_contents('php://input'), true);
            $sprintRepo->delete($data['sprint_id']);
            
            echo json_encode([
                'success' => true,
                'message' => 'Sprint supprimé avec succès'
            ]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    }
    
} catch (SprintConflictException $e) {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>