<?php
require_once __DIR__ . '/../repositories/Task.php';
require_once __DIR__ . '/../services/EmailService.php';
require_once __DIR__ . '/../core/Exceptions.php';

class TaskController
{
    private $taskRepo;

    public function __construct()
    {
        $this->taskRepo = new Task();
    }

    public function index()
    {
        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $result = $this->taskRepo->paginate($page, 10);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $result
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function search()
    {
        try {
            $filters = [
                'title' => $_GET['title'] ?? '',
                'status' => $_GET['status'] ?? '',
                'user_id' => $_GET['user_id'] ?? '',
                'sprint_id' => $_GET['sprint_id'] ?? ''
            ];

            $tasks = $this->taskRepo->search($filters);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $tasks
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function create()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $taskId = $this->taskRepo->create(
                $data['title'],
                $data['description'],
                $data['status'],
                $data['date_fin'],
                $data['sprint_id'],
                $data['user_id']
            );

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Tâche créée avec succès',
                'task_id' => $taskId
            ]);
        } catch (DuplicateException $e) {
            http_response_code(409);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update($taskId)
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $this->taskRepo->update(
                $taskId,
                $data['title'],
                $data['description'],
                $data['status'],
                $data['date_fin'],
                $data['sprint_id'],
                $data['user_id']
            );

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Tâche mise à jour avec succès'
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function delete($taskId)
    {
        try {
            $this->taskRepo->delete($taskId);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Tâche supprimée avec succès'
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function show($taskId)
    {
        try {
            $task = $this->taskRepo->show($taskId);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $task
            ]);
        } catch (NotFoundException $e) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
?>