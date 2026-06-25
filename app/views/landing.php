<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeWarden – Plateforme d'admission</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/landing.css">
</head>
<body>

<header class="landing-header">
    <div class="landing-logo">
        <img src="<?= BASE_URL ?>/public/assets/images/CWL.png" alt="Logo CodeWarden">
        <span class="landing-logo-text">CodeWarden</span>
    </div>
    <nav class="landing-header-links" aria-label="Accès rapide">
        <a class="btn-header-login" href="<?= BASE_URL ?>/login">Connexion</a>
        <a class="btn-header-login" href="<?= BASE_URL ?>/signup">Inscription</a>
    </nav>
</header>

<section class="landing-hero">
    <span class="hero-badge">Plateforme d'admission ESIEA</span>

    <h1 class="hero-title">
        Passez votre test d'admission<br>
        <span>en toute simplicité</span>
    </h1>

    <p class="hero-subtitle">
        CodeWarden vous accompagne tout au long de votre processus d'admission.
        Accédez à vos tests, suivez votre progression et recevez vos résultats.
    </p>

    <div class="hero-cta">
        <a class="btn-cta-primary" href="<?= BASE_URL ?>/signup">Créer un compte</a>
        <a class="btn-cta-secondary" href="<?= BASE_URL ?>/login">Se connecter</a>
    </div>
</section>

<div class="landing-features">
    <div class="feature-card">
        <div class="feature-icon">📋</div>
        <div class="feature-title">Tests en ligne</div>
        <div class="feature-desc">Passez vos QCM directement depuis votre navigateur</div>
    </div>
    <div class="feature-card">
        <div class="feature-icon">⏱</div>
        <div class="feature-title">Chrono intégré</div>
        <div class="feature-desc">Un minuteur vous guide pendant toute l'épreuve</div>
    </div>
    <div class="feature-card">
        <div class="feature-icon">📧</div>
        <div class="feature-title">Résultats par mail</div>
        <div class="feature-desc">Recevez vos résultats sous 48h directement par email</div>
    </div>
    <div class="feature-card">
        <div class="feature-icon">🔒</div>
        <div class="feature-title">Accès sécurisé</div>
        <div class="feature-desc">Vos données sont protégées et confidentielles</div>
    </div>
</div>

<footer class="landing-footer">
    &copy; <?= date('Y') ?> CodeWarden – ESIEA Paris
</footer>

</body>
</html>
