<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: profile.php');
    exit;
}

$error     = $_SESSION['login_error']    ?? '';
$success   = $_SESSION['signup_success'] ?? '';
$old_email = $_SESSION['login_email']    ?? '';
unset($_SESSION['login_error'], $_SESSION['signup_success'], $_SESSION['login_email']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Connexion</h1>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form id="login-form" action="process_login.php" method="post" novalidate>

        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($old_email) ?>">
        <span class="error" id="err-email"></span>

        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password">
        <span class="error" id="err-password"></span>

        <button type="submit">Se connecter</button>
    </form>

    <div class="link">
        Pas de compte ? <a href="signup.php">S'inscrire</a>
    </div>
</div>
<script src="validation.js"></script>
</body>
</html>
