<?php

class PasswordManager {

    //On hash le mot de passe soumis avec bcrypt
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    //On vérifie si le mot de passe soumis est le même que celui en base de données
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
}
?>
