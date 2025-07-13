<?php
/**
 * Gestionnaire de mots de passe sécurisé pour Caranille
 * Utilise bcrypt pour tous les nouveaux mots de passe
 */
class PasswordManager {
    
    /**
     * Hash un mot de passe avec password_hash (bcrypt)
     * @param string $password Le mot de passe en clair
     * @return string Le hash sécurisé
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    /**
     * Vérifie un mot de passe avec password_verify
     * @param string $password Le mot de passe en clair
     * @param string $hash Le hash stocké en base
     * @return bool True si le mot de passe correspond
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
}
?>