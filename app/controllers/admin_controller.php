<?php

$page = $_GET['page'] ?? 'exercices';

if ($page === 'creer_exercice_traitement') {
    require_once ROOT . '/app/controllers/jeux_controller.php';
     traiter_creation_jeux();
    exit;
}

if ($page === 'creer_test_traitement') {
    require_once ROOT . '/app/controllers/test_controller.php';
    traiter_creation_test();
    exit;
}

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


function tableau_de_bord()
{
    verifier_admin();

    $page = $_GET['page'] ?? 'exercices';

    // PAGE : CREER EXERCICE
    if ($page === 'creer_exercice') {

        require_once ROOT . '/app/models/jeux.php';

        // Récupération des ENUM
        $types = jeux_recuperer_types();
        $difficultes = jeux_recuperer_difficultes();

        // Envoi à la vue
        afficher_vue('admin/dashboard', [
            'page' => $page,
            'types' => $types,
            'difficultes' => $difficultes
        ]);

        return;
    }

    if ($page === 'creer_test') {

    require_once ROOT . '/app/models/jeux.php';
    require_once ROOT . '/app/models/candidat.php';

    // Récupérer les jeux actifs
    $jeux = jeux_tous_actifs();

    // Récupérer les candidats actifs
    $candidats = candidats_tous_actifs();

    afficher_vue('admin/dashboard', [
        'page' => $page,
        'jeux' => $jeux,
        'candidats' => $candidats
    ]);

    return;
}

    // AUTRES PAGES
    afficher_vue('admin/dashboard', [
        'page' => $page
    ]);
}


// GET /admin/logout — Déconnecte l'admin
function deconnecter_admin()
{
    session_destroy();
    header('Location: ' . BASE_URL . '/admin/login');
    exit;
}


