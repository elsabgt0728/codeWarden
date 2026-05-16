<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$email    = trim($_POST['email']    ?? '');
$password = $_POST['password']      ?? '';

if ($email === '' || $password === '') {
    $_SESSION['login_error'] = 'Veuillez remplir tous les champs.';
    $_SESSION['login_email'] = $email;
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM candidat WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    $_SESSION['login_error'] = 'E-mail ou mot de passe incorrect.';
    $_SESSION['login_email'] = $email;
    header('Location: login.php');
    exit;
}

session_regenerate_id(true);
$_SESSION['id_candidat'] = $user['id_candidat'];

header('Location: profile.php');
exit;
