<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration – CodeWarden</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/style.css">
</head>
<body>
<div class="container">
    <h1>Connexion Administration</h1>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/admin/login" method="post" novalidate>
        <?= csrf_champ() ?>

        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" autocomplete="email">
        <span class="error" id="err-email"></span>

        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password">
        <span class="error" id="err-password"></span>

        <button type="submit">Se connecter</button>
    </form>
</div>
</body>
</html>
