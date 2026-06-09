<?php
// Vérifie que le CANDIDAT est connecté, sinon redirige vers la connexion
function verifier_candidat()
{
    if (!isset($_SESSION['id_candidat'])) {
        header('Location: ' . BASE_URL . '/');
        exit;
    }
}

// Vérifie que l'ADMIN est connecté, sinon redirige vers la connexion admin
function verifier_admin()
{
    if (!isset($_SESSION['id_admin'])) {
        header('Location: ' . BASE_URL . '/admin/login');
        exit;
    }
}
