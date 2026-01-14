<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../entities/user.php';
require_once __DIR__ . '/../entities/admin.php';
require_once __DIR__ . '/../entities/membre.php';
require_once __DIR__ . '/../entities/projectchef.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    // ========== LOGIN ==========
    if ($method === 'POST' && $action === 'login') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Email et mot de passe requis']);
            exit();
        }
        
        // Try to login with all user types
        $userTypes = [
            'admin' => new admin(),
            'projectchef' => new projectchef(),
            'membre' => new membre()
        ];
        
        $loggedIn = false;
        $userObj = null;
        
        foreach ($userTypes as $type => $user) {
            try {
                if ($user->login($data['email'], $data['password'])) {
                    $loggedIn = true;
                    $userObj = $user;
                    break;
                }
            } catch (Exception $e) {
                continue;
            }
        }
        
        if ($loggedIn && $userObj) {
            session_start();
            $_SESSION['user_id'] = $userObj->get_userid();
            $_SESSION['user_nom'] = $userObj->get_nom();
            $_SESSION['user_email'] = $userObj->get_email();
            $_SESSION['user_role'] = $userObj->get_role();
            
            echo json_encode([
                'success' => true,
                'message' => 'Connexion réussie',
                'user' => [
                    'id' => $userObj->get_userid(),
                    'nom' => $userObj->get_nom(),
                    'email' => $userObj->get_email(),
                    'role' => $userObj->get_role()
                ]
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Email ou mot de passe incorrect']);
        }
    }
    
    // ========== SIGNUP ==========
    elseif ($method === 'POST' && $action === 'signup') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        if (empty($data['nom']) || empty($data['email']) || empty($data['password']) || empty($data['role'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Tous les champs sont requis']);
            exit();
        }
        
        // Validate role
        if (!in_array($data['role'], ['admin', 'projectchef', 'membre'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Rôle invalide']);
            exit();
        }
        
        // Validate email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Format d\'email invalide']);
            exit();
        }
        
        // Validate password length
        if (strlen($data['password']) < 6) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Le mot de passe doit contenir au moins 6 caractères']);
            exit();
        }
        
        try {
            // Create user based on role
            if ($data['role'] === 'admin') {
                $user = new admin();
            } elseif ($data['role'] === 'projectchef') {
                $user = new projectchef();
            } else {
                $user = new membre();
            }
            
            // Set user properties
            $user->set_nom($data['nom']);
            $user->set_email($data['email']);
            $user->set_password($data['password']); // This should hash the password in the entity
            $user->set_role($data['role']);
            
            // Create user in database
            $userId = $user->create();
            
            if ($userId) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Compte créé avec succès! Vous pouvez maintenant vous connecter.',
                    'user_id' => $userId
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la création du compte']);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            
            // Check for duplicate email error
            $errorMessage = $e->getMessage();
            if (strpos($errorMessage, 'Duplicate') !== false || 
                strpos($errorMessage, 'duplicate') !== false ||
                strpos($errorMessage, '1062') !== false) {
                echo json_encode(['success' => false, 'message' => 'Cet email est déjà utilisé']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur: ' . $errorMessage]);
            }
        }
    }
    
    // ========== LOGOUT ==========
    elseif ($method === 'POST' && $action === 'logout') {
        session_start();
        session_unset();
        session_destroy();
        echo json_encode(['success' => true, 'message' => 'Déconnexion réussie']);
    }
    
    // ========== INVALID ACTION ==========
    else {
        http_response_code(404);
        echo json_encode([
            'success' => false, 
            'message' => 'Action non trouvée',
            'allowed_actions' => ['login', 'signup', 'logout']
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
}
?>