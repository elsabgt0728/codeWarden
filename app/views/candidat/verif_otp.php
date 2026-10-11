<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification – CodeWarden</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/style.css">
</head>
<body>
<div class="container">
    <h1>Vérification</h1>

    <p style="font-size:14px;color:#6b7280;margin-bottom:20px;">
        Un code à 6 chiffres a été envoyé à <strong><?= htmlspecialchars($email ?? '') ?></strong>.
        Saisis-le ci-dessous pour accéder à ton test (valable 10 minutes).
    </p>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/candidat/verifier-code" method="post" novalidate>
        <?= csrf_champ() ?>
        <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

        <label for="code">Code de vérification</label>
        <input type="text" id="code" name="code" inputmode="numeric" pattern="[0-9]{6}"
               maxlength="6" autocomplete="one-time-code" autofocus
               style="letter-spacing:8px;font-size:22px;text-align:center;">

        <button type="submit">Valider</button>
    </form>

    <form action="<?= BASE_URL ?>/candidat/renvoyer-code" method="post" style="margin-top:12px;">
        <?= csrf_champ() ?>
        <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">
        <button type="submit" style="background:none;border:none;color:#0091e6;text-decoration:underline;cursor:pointer;padding:0;font-size:13px;">
            Je n'ai pas reçu de code / renvoyer un code
        </button>
    </form>
</div>
</body>
</html>
