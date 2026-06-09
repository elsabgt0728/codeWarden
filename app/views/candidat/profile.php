<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon profil – CodeWarden</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/style.css">
</head>
<body>
<div class="container">
    <h1>Mon profil</h1>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form id="profile-form" action="<?= BASE_URL ?>/profile" method="post" novalidate>

        <label for="prenom">Prénom</label>
        <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($form['prenom'] ?? '') ?>">
        <span class="error" id="err-prenom"><?= htmlspecialchars($errors['prenom'] ?? '') ?></span>

        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($form['nom'] ?? '') ?>">
        <span class="error" id="err-nom"><?= htmlspecialchars($errors['nom'] ?? '') ?></span>

        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($form['email'] ?? '') ?>">
        <span class="error" id="err-email"><?= htmlspecialchars($errors['email'] ?? '') ?></span>

        <button type="submit">Enregistrer</button>
    </form>

    <div class="link">
        <a href="<?= BASE_URL ?>/logout">Se déconnecter</a>
    </div>
</div>
<script src="<?= BASE_URL ?>/public/assets/js/validation.js"></script>
</body>
</html>
