<?php
// Affiche un fichier de vue en lui transmettant des variables
// Ex: afficher_vue('auth/login', ['error' => 'Oops']) → $error = 'Oops' dans la vue
function afficher_vue($vue, $donnees = [])
{
    extract($donnees);
    require ROOT . '/app/views/' . $vue . '.php';
}

// ----------------------------------------------------------------------
// Protection CSRF
// ----------------------------------------------------------------------

// Retourne le jeton CSRF de la session en cours (le génère s'il n'existe pas encore)
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// À placer juste après la balise <form ...> de chaque formulaire POST
function csrf_champ(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

// À appeler en tout début de chaque fonction qui traite un POST venant d'un
// formulaire HTML classique. Coupe la requête si le jeton est absent, invalide
// ou ne correspond pas à celui généré pour cette session.
function verifier_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if ($token === '' || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        die('Requête invalide ou expirée (jeton de sécurité manquant). Rechargez la page et réessayez.');
    }
}
