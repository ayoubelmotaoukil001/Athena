<?php
require_once __DIR__ . '/../entities/admin.php';
require_once __DIR__ . '/../entities/projectchef.php';
require_once __DIR__ . '/../entities/membre.php';

class AuthController
{
    public function login()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';

            $admin = new admin();
            if ($admin->login($email, $password)) {
                session_start();
                $_SESSION['user_id'] = $admin->get_userid();
                $_SESSION['user_nom'] = $admin->get_nom();
                $_SESSION['user_role'] = $admin->get_role();

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Connexion réussie',
                    'user' => [
                        'id' => $admin->get_userid(),
                        'nom' => $admin->get_nom(),
                        'email' => $admin->get_email(),
                        'role' => $admin->get_role()
                    ]
                ]);
                return;
            }

            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Email ou mot de passe incorrect'
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function logout()
    {
        session_start();
        session_destroy();
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Déconnexion réussie'
        ]);
    }

    public function register()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $user = new membre();
            $user->set_nom($data['nom']);
            $user->set_email($data['email']);
            $user->set_password($data['password']);
            $user->set_role($data['role'] ?? 'membre');
            
            $userId = $user->create();

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Compte créé avec succès',
                'user_id' => $userId
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