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


// GET /admin/login — Affiche la page de connexion admin
function page_connexion_admin()
{
    if (isset($_SESSION['id_admin'])) {
        header('Location: ' . BASE_URL . '/admin');
        exit;
    }

    $error = $_SESSION['admin_error'] ?? '';
    unset($_SESSION['admin_error']);

    afficher_vue('admin/login', ['error' => $error]);
}

// POST /admin/login — Traite la connexion admin
function traiter_connexion_admin()
{
    $email      = trim($_POST['email'] ?? '');
    $motDePasse = $_POST['password'] ?? '';

    if ($email === '' || $motDePasse === '') {
        $_SESSION['admin_error'] = 'Veuillez remplir tous les champs.';
        header('Location: ' . BASE_URL . '/admin/login');
        exit;
    }

    $adminUser = trouver_admin_par_email($email);

    if ($adminUser === false || !password_verify($motDePasse, $adminUser['mot_de_passe'])) {
        $_SESSION['admin_error'] = 'E-mail ou mot de passe incorrect.';
        header('Location: ' . BASE_URL . '/admin/login');
        exit;
    }

    session_regenerate_id(true);
    $_SESSION['id_admin']   = $adminUser['id_admin'];
    $_SESSION['admin_role'] = $adminUser['role']; // super_admin, admin, moderateur

    header('Location: ' . BASE_URL . '/admin');
    exit;
}

function tableau_de_bord()
{
    verifier_admin();

    $page     = $_GET['page'] ?? 'exercices';
    $id_admin = $_SESSION['id_admin'];

    // Récupération des exercices et tests depuis la BDD
    $exercices = lister_jeux_admin($id_admin);
    $tests     = lister_test_admin($id_admin);

    // Exercice sélectionné
    $exercice_selectionne = null;
    if (isset($_GET['exercice_id'])) {
        $id_cible = (int) $_GET['exercice_id'];
        foreach ($exercices as $ex) {
            if ($ex['id_jeux'] === $id_cible) {
                $exercice_selectionne = $ex;
                break;
            }
        }
    }

    // Test sélectionné
    $test_selectionne = null;
    if (isset($_GET['test_id'])) {
        $id_cible = (int) $_GET['test_id'];
        foreach ($tests as $t) {
            if ($t['id_test'] === $id_cible) {
                $test_selectionne = $t;
                break;
            }
        }
    }

    if ($page === 'creer_exercice') {

        require_once ROOT . '/app/models/jeux.php';

        $types       = jeux_recuperer_types();
        $difficultes = jeux_recuperer_difficultes();

        afficher_vue('admin/dashboard', [
            'page'                 => $page,
            'exercices'            => $exercices,
            'tests'                => $tests,
            'exercice_selectionne' => $exercice_selectionne,
            'test_selectionne'     => $test_selectionne,
            'types'                => $types,
            'difficultes'          => $difficultes,
        ]);

        return;
    }

    if ($page === 'creer_test') {

        require_once ROOT . '/app/models/jeux.php';
        require_once ROOT . '/app/models/candidat.php';

        $jeux      = jeux_tous_actifs();
        $candidats = candidats_tous_actifs();

        afficher_vue('admin/dashboard', [
            'page'                 => $page,
            'exercices'            => $exercices,
            'tests'                => $tests,
            'exercice_selectionne' => $exercice_selectionne,
            'test_selectionne'     => $test_selectionne,
            'jeux'                 => $jeux,
            'candidats'            => $candidats,
        ]);

        return;
    }

    afficher_vue('admin/dashboard', [
        'page'                 => $page,
        'exercices'            => $exercices,
        'tests'                => $tests,
        'exercice_selectionne' => $exercice_selectionne,
        'test_selectionne'     => $test_selectionne,
    ]);
}

function deconnecter_admin()
{
    session_destroy();
    header('Location: ' . BASE_URL . '/admin/login');
    exit;
}

