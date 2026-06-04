<?php
// Pages de l'espace personnel du candidat

// GET /profile — Affiche le profil
function page_profil()
{
    verifier_candidat();

    $user    = trouver_candidat_par_id($_SESSION['id_candidat']);
    $errors  = isset($_SESSION['profile_errors'])  ? $_SESSION['profile_errors']  : [];
    $success = isset($_SESSION['profile_success']) ? $_SESSION['profile_success'] : '';
    $form    = isset($_SESSION['profile_form'])    ? $_SESSION['profile_form']    : $user;
    unset($_SESSION['profile_errors'], $_SESSION['profile_success'], $_SESSION['profile_form']);

    afficher_vue('candidat/profile', [
        'user'    => $user,
        'errors'  => $errors,
        'success' => $success,
        'form'    => $form,
    ]);
}

// POST /profile — Enregistre les modifications du profil
function modifier_profil()
{
    verifier_candidat();

    $prenom = trim($_POST['prenom'] ?? '');
    $nom    = trim($_POST['nom']    ?? '');
    $email  = trim($_POST['email']  ?? '');
    $userId = $_SESSION['id_candidat'];

    $errors = [];

    if ($prenom === '') $errors['prenom'] = 'Le prénom est obligatoire.';
    if ($nom    === '') $errors['nom']    = 'Le nom est obligatoire.';

    if ($email === '') {
        $errors['email'] = "L'e-mail est obligatoire.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Adresse e-mail invalide.';
    } elseif (email_deja_pris($email, $userId)) {
        $errors['email'] = 'Cet e-mail est déjà utilisé.';
    }

    if (!empty($errors)) {
        $_SESSION['profile_errors'] = $errors;
        $_SESSION['profile_form']   = ['prenom' => $prenom, 'nom' => $nom, 'email' => $email];
        header('Location: ' . BASE_URL . '/profile');
        exit;
    }

    modifier_candidat($userId, $nom, $prenom, $email);
    $_SESSION['profile_success'] = 'Profil mis à jour.';
    header('Location: ' . BASE_URL . '/profile');
    exit;
}
