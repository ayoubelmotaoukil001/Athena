<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../repositories/Project.php';
require_once __DIR__ . '/../core/Exceptions.php';

$method = $_SERVER['REQUEST_METHOD'];
$projectRepo = new Project();

try {
    switch ($method) {
        case 'GET':
            if (isset($_GET['id'])) {
                $project = $projectRepo->show($_GET['id']);
                echo json_encode(['success' => true, 'data' => $project]);
            } else {
                $projects = $projectRepo->getAll();
                echo json_encode(['success' => true, 'data' => $projects]);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                throw new Exception('Invalid JSON data');
            }
            
            $projectId = $projectRepo->create(
                $data['titre'],
                $data['description'] ?? '',
                $data['etat'] ?? 'actif',
                $data['chef_id']
            );
            
            echo json_encode([
                'success' => true,
                'message' => 'Projet créé avec succès',
                'project_id' => $projectId
            ]);
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            
            $projectRepo->update(
                $data['projet_id'],
                $data['titre'],
                $data['description'] ?? '',
                $data['etat'] ?? 'actif',
                $data['chef_id']
            );
            
            echo json_encode([
                'success' => true,
                'message' => 'Projet mis à jour avec succès'
            ]);
            break;

        case 'DELETE':
            $data = json_decode(file_get_contents('php://input'), true);
            $projectRepo->delete($data['projet_id']);
            
            echo json_encode([
                'success' => true,
                'message' => 'Projet supprimé avec succès'
            ]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>