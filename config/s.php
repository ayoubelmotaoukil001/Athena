<?php
session_start();

require_once __DIR__ . '/db.php';

if (!isset($_SESSION['user_id']) && basename($_SERVER['PHP_SELF']) !== 'login.php') {
    header('Location: ../views/login.php');
    exit();
}

if (isset($_SESSION['user_id'])) {
    $db = db::getInstance()->getConnection();
    $stm = $db->prepare("SELECT is_active FROM user WHERE id = ?");
    $stm->execute([$_SESSION['user_id']]);
    $user = $stm->fetch(PDO::FETCH_ASSOC);
    
    if (!$user || $user['is_active'] == 0) {
        session_destroy();
        header('Location: ../views/login.php');
        exit();
    }
}
?>