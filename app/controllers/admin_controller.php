<?php

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
    $_SESSION['admin_role'] = $adminUser['role'];

    header('Location: ' . BASE_URL . '/admin');
    exit;
}

function tableau_de_bord()
{
    verifier_admin();

    $page     = $_GET['page'] ?? 'exercices';
    $id_admin = $_SESSION['id_admin'];

    $exercices = lister_jeux_admin($id_admin);
    $tests     = lister_test_admin($id_admin);

    $exercice_selectionne = null;
    if (isset($_GET['exercice_id'])) {
        $id_cible = (int) $_GET['exercice_id'];
        foreach ($exercices as $ex) {
            if ((int)$ex['id_jeux'] === $id_cible) { $exercice_selectionne = $ex; break; }
        }
    }

    $test_selectionne = null;
    if (isset($_GET['test_id'])) {
        $id_cible = (int) $_GET['test_id'];
        foreach ($tests as $t) {
            if ((int)$t['id_test'] === $id_cible) { $test_selectionne = $t; break; }
        }
    }

    $base = [
        'page'                 => $page,
        'exercices'            => $exercices,
        'tests'                => $tests,
        'exercice_selectionne' => $exercice_selectionne,
        'test_selectionne'     => $test_selectionne,
    ];

    if ($page === 'creer_exercice') {
        require_once ROOT . '/app/models/jeux.php';
        afficher_vue('admin/dashboard', $base + [
            'types'       => jeux_recuperer_types(),
            'difficultes' => jeux_recuperer_difficultes(),
        ]);
        return;
    }

    if ($page === 'creer_test') {
        require_once ROOT . '/app/models/jeux.php';
        require_once ROOT . '/app/models/candidat.php';
        afficher_vue('admin/dashboard', $base + [
            'jeux'      => jeux_tous_actifs(),
            'candidats' => candidats_tous_actifs(),
        ]);
        return;
    }

    if ($page === 'statistiques') {
        require_once ROOT . '/app/models/stats.php';
        afficher_vue('admin/dashboard', $base + [
            'stats_globales' => stats_admin_global([]),
            'stats_tests'    => stats_admin_comparaison([]),
        ]);
        return;
    }

    if ($page === 'etudiants') {
        require_once ROOT . '/app/models/resultat.php';
        afficher_vue('admin/dashboard', $base + [
            'candidats_liste' => resultats_candidats_liste(),
        ]);
        return;
    }

    afficher_vue('admin/dashboard', $base);
}

function admin_decision_candidat()
{
    verifier_admin();
    require_once ROOT . '/app/models/resultat.php';
    require_once ROOT . '/app/models/passageTest.php';
    require_once ROOT . '/app/helpers/mailer.php';

    $id_passage = (int)($_POST['id_passage_test'] ?? 0);
    $decision   = $_POST['decision'] ?? '';
    $valides    = ['admis', 'refuse', 'liste_attente', 'en_attente'];

    if ($id_passage && in_array($decision, $valides)) {
        resultat_mettre_a_jour_decision($id_passage, $decision);

        $infos = passage_candidat_et_test($id_passage);
        if ($infos) {
            envoyer_decision(
                $infos['email'],
                $infos['prenom'],
                $infos['nom'],
                $infos['titre_test'],
                $decision
            );
        }
    }

    header('Location: ' . BASE_URL . '/admin?page=etudiants');
    exit;
}

function deconnecter_admin()
{
    session_destroy();
    header('Location: ' . BASE_URL . '/admin/login');
    exit;
}
