<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: signup.php');
    exit;
}

$prenom  = trim($_POST['prenom']  ?? '');
$nom     = trim($_POST['nom']     ?? '');
$email   = trim($_POST['email']   ?? '');
$password = $_POST['password']    ?? '';
$confirm  = $_POST['confirm']     ?? '';

$errors = [];

if ($prenom === '') $errors['prenom'] = 'Le prénom est obligatoire.';
if ($nom === '')    $errors['nom']    = 'Le nom est obligatoire.';

if ($email === '') {
    $errors['email'] = 'L\'e-mail est obligatoire.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Adresse e-mail invalide.';
}

if ($password === '') {
    $errors['password'] = 'Le mot de passe est obligatoire.';
} elseif (strlen($password) < 8) {
    $errors['password'] = 'Minimum 8 caractères.';
}

if ($confirm === '') {
    $errors['confirm'] = 'Veuillez confirmer le mot de passe.';
} elseif ($password !== $confirm) {
    $errors['confirm'] = 'Les mots de passe ne correspondent pas.';
}

if (empty($errors)) {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errors['email'] = 'Cet e-mail est déjà utilisé.';
    }
}

if (!empty($errors)) {
    $_SESSION['signup_errors'] = $errors;
    $_SESSION['signup_old']    = compact('prenom', 'nom', 'email');
    header('Location: signup.php');
    exit;
}

$hash = password_hash($password, PASSWORD_BCRYPT);
$pdo->prepare('INSERT INTO users (nom, prenom, email, password) VALUES (:nom, :prenom, :email, :password)')->execute([
    ':nom' => $nom,
    ':prenom' => $prenom,
    ':email' => $email,
    ':password' => $hash
]);

$_SESSION['signup_success'] = 'Compte créé. Vous pouvez vous connecter.';
header('Location: login.php');
exit;
