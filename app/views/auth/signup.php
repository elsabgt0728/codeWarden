<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription – CodeWarden</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/style.css">
</head>
<body>
<div class="container">
    <h1>Inscription</h1>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form id="signup-form" action="<?= BASE_URL ?>/signup" method="post" novalidate>
        <?= csrf_champ() ?>

        <label for="prenom">Prénom</label>
        <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($form['prenom'] ?? '') ?>">
        <span class="error" id="err-prenom"><?= htmlspecialchars($errors['prenom'] ?? '') ?></span>

        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($form['nom'] ?? '') ?>">
        <span class="error" id="err-nom"><?= htmlspecialchars($errors['nom'] ?? '') ?></span>

        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($form['email'] ?? '') ?>">
        <span class="error" id="err-email"><?= htmlspecialchars($errors['email'] ?? '') ?></span>

        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password">
        <span class="error" id="err-password"><?= htmlspecialchars($errors['password'] ?? '') ?></span>

        <label for="confirm">Confirmer le mot de passe</label>
        <input type="password" id="confirm" name="confirm">
        <span class="error" id="err-confirm"><?= htmlspecialchars($errors['confirm'] ?? '') ?></span>

        <button type="submit">S'inscrire</button>
    </form>

    <div class="link">
        Déjà inscrit ? <a href="<?= BASE_URL ?>/login">Se connecter</a>
    </div>
</div>
<script src="<?= BASE_URL ?>/public/assets/js/validation.js"></script>
</body>
</html>
