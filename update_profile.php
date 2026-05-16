<?php
session_start();

if (!isset($_SESSION['id_candidat'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: profile.php');
    exit;
}

require 'db_connect.php';

$prenom = trim($_POST['prenom'] ?? '');
$nom    = trim($_POST['nom']    ?? '');
$email  = trim($_POST['email']  ?? '');
$userId = $_SESSION['id_candidat'];

$errors = [];

if ($prenom === '') $errors['prenom'] = 'Le prénom est obligatoire.';
if ($nom === '')    $errors['nom']    = 'Le nom est obligatoire.';

if ($email === '') {
    $errors['email'] = 'L\'e-mail est obligatoire.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Adresse e-mail invalide.';
} else {
    $stmt = $pdo->prepare('SELECT id_candidat FROM candidat WHERE email = ? AND id_candidat != ?');
    $stmt->execute([$email, $userId]);
    if ($stmt->fetch()) {
        $errors['email'] = 'Cet e-mail est déjà utilisé.';
    }
}

if (!empty($errors)) {
    $_SESSION['profile_errors'] = $errors;
    $_SESSION['profile_old']    = compact('prenom', 'nom', 'email');
    header('Location: profile.php');
    exit;
}

$pdo->prepare('UPDATE candidat SET nom = ?, prenom = ?, email = ? WHERE id_candidat = ?')->execute([$nom, $prenom, $email, $userId]);

$_SESSION['profile_success'] = 'Profil mis à jour.';
header('Location: profile.php');
exit;
