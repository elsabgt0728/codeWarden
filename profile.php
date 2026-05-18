<?php
session_start();

if (!isset($_SESSION['id_candidat'])) {
    header('Location: login.php');
    exit;
}

require 'db_connect.php';

$stmt = $pdo->prepare('SELECT * FROM candidat WHERE id_candidat = ?');
$stmt->execute([$_SESSION['id_candidat']]);
$user = $stmt->fetch();

$errors  = $_SESSION['profile_errors']  ?? [];
$success = $_SESSION['profile_success'] ?? '';
$old     = $_SESSION['profile_old']     ?? $user;
unset($_SESSION['profile_errors'], $_SESSION['profile_success'], $_SESSION['profile_old']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon profil</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Mon profil</h1>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form id="profile-form" action="update_profile.php" method="post" novalidate>

        <label for="prenom">Prénom</label>
        <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($old['prenom']) ?>">
        <span class="error" id="err-prenom"><?= htmlspecialchars($errors['prenom'] ?? '') ?></span>

        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($old['nom']) ?>">
        <span class="error" id="err-nom"><?= htmlspecialchars($errors['nom'] ?? '') ?></span>

        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($old['email']) ?>">
        <span class="error" id="err-email"><?= htmlspecialchars($errors['email'] ?? '') ?></span>

        <button type="submit">Enregistrer</button>
    </form>

    <div class="link">
        <a href="logout.php">Se déconnecter</a>
    </div>
</div>
<script src="validation.js"></script>
</body>
</html>
