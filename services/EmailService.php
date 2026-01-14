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
