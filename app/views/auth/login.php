<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion – CodeWarden</title>

    <!-- Ton CSS harmonisé -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/style.css">
</head>

<body>

<div class="container">

    <h1>Connexion</h1>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form id="login-form" action="<?= BASE_URL ?>/login" method="post" novalidate>

        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>">
        <span class="error" id="err-email"></span>

        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password">
        <span class="error" id="err-password"></span>

        <button type="submit">Se connecter</button>
    </form>

    <div class="link">
        Pas de compte ?
        <a href="<?= BASE_URL ?>/public/index.php/signup">S'inscrire</a>
    </div>

</div>

<script src="<?= BASE_URL ?>/public/assets/js/validation.js"></script>

</body>
</html>
