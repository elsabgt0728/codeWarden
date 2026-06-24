<?php
// Connexion, inscription et déconnexion des candidats

// GET / — Page d'accueil publique
function page_accueil()
{
    if (isset($_SESSION['id_candidat'])) {
        header('Location: ' . BASE_URL . '/candidat');
        exit;
    }
    if (isset($_SESSION['id_admin'])) {
        header('Location: ' . BASE_URL . '/admin');
        exit;
    }
    afficher_vue('landing');
}

// GET /login — Affiche le formulaire de connexion
function page_connexion()
{
    if (isset($_SESSION['id_candidat'])) {
        header('Location: ' . BASE_URL . '/candidat');
        exit;
    }

    $error   = isset($_SESSION['login_error'])    ? $_SESSION['login_error']    : '';
    $success = isset($_SESSION['signup_success']) ? $_SESSION['signup_success'] : '';
    $email   = isset($_SESSION['login_email'])    ? $_SESSION['login_email']    : '';
    unset($_SESSION['login_error'], $_SESSION['signup_success'], $_SESSION['login_email']);

    afficher_vue('auth/login', [
        'error'   => $error,
        'success' => $success,
        'email'   => $email,
    ]);
}

// POST /login — Traite le formulaire de connexion
function traiter_connexion()
{
    $email      = trim($_POST['email']    ?? '');
    $motDePasse = $_POST['password']      ?? '';

    if ($email === '' || $motDePasse === '') {
        $_SESSION['login_error'] = 'Veuillez remplir tous les champs.';
        $_SESSION['login_email'] = $email;
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    $user = trouver_candidat_par_email($email);

    if ($user === false || !password_verify($motDePasse, $user['password'])) {
        $_SESSION['login_error'] = 'E-mail ou mot de passe incorrect.';
        $_SESSION['login_email'] = $email;
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    session_regenerate_id(true); // Sécurité : renouvelle l'ID de session à chaque connexion
    $_SESSION['id_candidat'] = $user['id_candidat'];
    header('Location: ' . BASE_URL . '/candidat');
    exit;
}

// GET /signup — Affiche la page d'inscription
function page_inscription()
{
    if (isset($_SESSION['id_candidat'])) {
        header('Location: ' . BASE_URL . '/candidat');
        exit;
    }

    $errors  = isset($_SESSION['signup_errors']) ? $_SESSION['signup_errors'] : [];
    $form    = isset($_SESSION['signup_form'])   ? $_SESSION['signup_form']   : [];
    $success = isset($_SESSION['signup_success']) ? $_SESSION['signup_success'] : '';
    unset($_SESSION['signup_errors'], $_SESSION['signup_form'], $_SESSION['signup_success']);

    afficher_vue('auth/signup', [
        'errors'  => $errors,
        'form'    => $form,
        'success' => $success,
    ]);
}

// POST /signup — Traite le formulaire d'inscription
function traiter_inscription()
{
    $prenom     = trim($_POST['prenom']   ?? '');
    $nom        = trim($_POST['nom']      ?? '');
    $email      = trim($_POST['email']    ?? '');
    $motDePasse = $_POST['password']      ?? '';
    $confirm    = $_POST['confirm']       ?? '';

    $errors = [];

    if ($prenom === '') $errors['prenom'] = 'Le prénom est obligatoire.';
    if ($nom    === '') $errors['nom']    = 'Le nom est obligatoire.';

    if ($email === '') {
        $errors['email'] = "L'e-mail est obligatoire.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Adresse e-mail invalide.';
    }

    if ($motDePasse === '') {
        $errors['password'] = 'Le mot de passe est obligatoire.';
    } elseif (strlen($motDePasse) < 8) {
        $errors['password'] = 'Minimum 8 caractères.';
    }

    if ($confirm === '') {
        $errors['confirm'] = 'Veuillez confirmer le mot de passe.';
    } elseif ($motDePasse !== $confirm) {
        $errors['confirm'] = 'Les mots de passe ne correspondent pas.';
    }

    if (empty($errors) && trouver_candidat_par_email($email) !== false) {
        $errors['email'] = 'Cet e-mail est déjà utilisé.';
    }

    if (!empty($errors)) {
        $_SESSION['signup_errors'] = $errors;
        $_SESSION['signup_form']   = ['prenom' => $prenom, 'nom' => $nom, 'email' => $email];
        header('Location: ' . BASE_URL . '/signup');
        exit;
    }

    // On ne stocke jamais un mot de passe en clair — on le hache avec bcrypt
    creer_candidat($nom, $prenom, $email, password_hash($motDePasse, PASSWORD_BCRYPT));
    $_SESSION['signup_success'] = 'Compte créé. Vous pouvez vous connecter.';
    header('Location: ' . BASE_URL . '/login');
    exit;
}

// GET /logout — Déconnecte le candidat
function deconnecter()
{
    session_destroy();
    header('Location: ' . BASE_URL . '/');
    exit; // retour à la landing page
}
