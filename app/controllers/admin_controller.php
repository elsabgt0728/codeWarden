<?php
// Connexion et tableau de bord admin

// GET /admin/login — Affiche la page de connexion admin
function page_connexion_admin()
{
    if (isset($_SESSION['id_admin'])) {
        header('Location: ' . BASE_URL . '/admin');
        exit;
    }

    $error = isset($_SESSION['admin_error']) ? $_SESSION['admin_error'] : '';
    unset($_SESSION['admin_error']);

    afficher_vue('admin/login', ['error' => $error]);
}

// POST /admin/login — Traite la connexion admin
function traiter_connexion_admin()
{
    $email      = trim($_POST['email']    ?? '');
    $motDePasse = $_POST['password']      ?? '';

    if ($email === '' || $motDePasse === '') {
        $_SESSION['admin_error'] = 'Veuillez remplir tous les champs.';
        header('Location: ' . BASE_URL . '/admin/login');
        exit;
    }

    $adminUser = trouver_admin_par_email($email);

    // La colonne mot de passe s'appelle 'mot_de_passe' dans la table administrateur
    if ($adminUser === false || !password_verify($motDePasse, $adminUser['mot_de_passe'])) {
        $_SESSION['admin_error'] = 'E-mail ou mot de passe incorrect.';
        header('Location: ' . BASE_URL . '/admin/login');
        exit;
    }

    session_regenerate_id(true);
    $_SESSION['id_admin']   = $adminUser['id_admin'];
    $_SESSION['admin_role'] = $adminUser['role']; // super_admin, admin ou moderateur
    header('Location: ' . BASE_URL . '/admin');
    exit;
}

// GET /admin — Tableau de bord
function tableau_de_bord()
{
    verifier_admin();

    $page = isset($_GET['page']) ? $_GET['page'] : 'exercices';
    afficher_vue('admin/dashboard', ['page' => $page]);
}

// GET /admin/logout — Déconnecte l'admin
function deconnecter_admin()
{
    session_destroy();
    header('Location: ' . BASE_URL . '/admin/login');
    exit;
}

// GET /admin/jeux/creer — Formulaire de création d'un jeu de logique
function page_creer_jeu()
{
    verifier_admin();

    afficher_vue('admin/formulaireDeCreationJeux');
}
