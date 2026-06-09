<?php
// Retourne la connexion PDO à MySQL (créée une seule fois)
function connecter_bdd()
{
    static $connexion = null;

    if ($connexion === null) {
        $config = require ROOT . '/config/config.php';

        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";

        try {
            $connexion = new PDO($dsn, $config['user'], $config['password']);
            $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $connexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Erreur de connexion : ' . $e->getMessage());
        }
    }

    return $connexion;
}
