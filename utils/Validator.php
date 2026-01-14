<?php
class Validator
{
    public static function validateEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException("Email invalide");
        }
        return true;
    }

    public static function validateRequired($value, $fieldName)
    {
        if (empty(trim($value))) {
            throw new ValidationException("Le champ $fieldName est requis");
        }
        return true;
    }

    public static function validateDate($date)
    {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        if (!$d || $d->format('Y-m-d') !== $date) {
            throw new ValidationException("Format de date invalide");
        }
        return true;
    }

    public static function validatePassword($password)
    {
        if (strlen($password) < 6) {
            throw new ValidationException("Le mot de passe doit contenir au moins 6 caractères");
        }
        return true;
    }

    public static function validateRole($role)
    {
        $validRoles = ['admin', 'projectchef', 'membre'];
        if (!in_array($role, $validRoles)) {
            throw new ValidationException("Rôle invalide");
        }
        return true;
    }
}
?>