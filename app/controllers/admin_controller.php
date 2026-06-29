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
    $_SESSION['admin_role'] = $adminUser['role'];
    header('Location: ' . BASE_URL . '/admin');
    exit;
}

function tableau_de_bord()
{
    verifier_admin();

    $page = $_GET['page'] ?? 'exercices';
    $id   = $_GET['id']   ?? null;

    $liste_jeux = lister_jeux();
    $tests      = lister_tests();
    $candidats  = lister_candidats();

    $errors_test        = $_SESSION['creation_test_errors']    ?? [];
    $old_test           = $_SESSION['creation_test_old']       ?? [];
    $errors_attribution = $_SESSION['attribution_test_errors'] ?? [];
    $old_attribution    = $_SESSION['attribution_test_old']    ?? [];
    $errors_jeu         = $_SESSION['creation_jeu_errors']     ?? [];
    $old_jeu            = $_SESSION['creation_jeu_old']        ?? [];
    $success            = $_SESSION['admin_success']           ?? '';

    unset(
        $_SESSION['creation_test_errors'],    $_SESSION['creation_test_old'],
        $_SESSION['attribution_test_errors'], $_SESSION['attribution_test_old'],
        $_SESSION['creation_jeu_errors'],     $_SESSION['creation_jeu_old'],
        $_SESSION['admin_success']
    );

    $epreuve      = null;
    $jeux_epreuve = [];
    if ($page === 'epreuve' && $id) {
        $epreuve = trouver_test_par_id($id);
        if ($epreuve) {
            $jeux_epreuve = lister_jeux_du_test($id);
        }
    }

    afficher_vue('admin/dashboard', compact(
        'page', 'id',
        'liste_jeux', 'tests', 'candidats',
        'errors_test', 'old_test',
        'errors_attribution', 'old_attribution',
        'errors_jeu', 'old_jeu',
        'success',
        'epreuve', 'jeux_epreuve'
    ));
}

function deconnecter_admin()
{
    session_destroy();
    header('Location: ' . BASE_URL . '/admin/login');
    exit;
}

// POST /admin/jeux/creer — Traite la création d'un exercice
function traiter_creation_jeu()
{
    verifier_admin();

    $titre      = trim($_POST['titre']      ?? '');
    $type       = trim($_POST['type']       ?? '');
    $difficulte = trim($_POST['difficulte'] ?? 'moyen');
    $bareme     = trim($_POST['bareme']     ?? '');
    $statut     = trim($_POST['statut']     ?? 'brouillon');

    $description = trim($_POST['description'] ?? '');
    $nbquestions = max(0, (int)($_POST['nbquestions'] ?? 0));
    $questions   = [];
    for ($i = 1; $i <= $nbquestions; $i++) {
        $questions[] = [
            'intitule'      => trim($_POST["question_intitule_$i"] ?? ''),
            'points'        => (int)($_POST["question_points_$i"]  ?? 1),
            'type'          => trim($_POST["question_type_$i"]     ?? 'texte'),
            'bonne_reponse' => trim($_POST["question_bonne_$i"]    ?? ''),
        ];
    }
    $contenu_json = json_encode(['description' => $description, 'questions' => $questions]);

    $types_valides = ['qcm', 'texte_libre', 'glisser_deposer', 'association', 'autre'];
    $errors = [];
    if ($titre === '') {
        $errors['titre'] = 'Le titre est obligatoire.';
    }
    if (!in_array($type, $types_valides)) {
        $errors['type'] = 'Sélectionnez un type valide.';
    }
    if ($bareme === '' || !ctype_digit($bareme) || (int)$bareme < 1) {
        $errors['bareme'] = 'Le barème doit être un entier ≥ 1.';
    }

    if (!empty($errors)) {
        $_SESSION['creation_jeu_errors'] = $errors;
        $_SESSION['creation_jeu_old']    = compact('titre', 'type', 'difficulte', 'bareme', 'statut', 'description');
        header('Location: ' . BASE_URL . '/admin?page=creer_exercice');
        exit;
    }
 

    creer_jeu($titre, $type, $difficulte, (int)$bareme, $statut, $contenu_json, $_SESSION['id_admin']);
    $_SESSION['admin_success'] = 'Exercice créé avec succès.';
    header('Location: ' . BASE_URL . '/admin?page=liste_exercice');
    exit;
}

// POST /admin/tests/creer — Traite la création d'un test
function traiter_creation_test()
{
    verifier_admin();

    $titre = trim($_POST['titre'] ?? '');
    $duree = trim($_POST['duree'] ?? '');
    $jeux  = $_POST['jeux']       ?? [];

    $errors = [];
    if ($titre === '') {
        $errors['titre'] = 'Le titre est obligatoire.';
    }
    if ($duree === '' || !ctype_digit($duree) || (int)$duree < 1) {
        $errors['duree'] = 'La durée doit être un nombre de minutes > 0.';
    }
    if (empty($jeux)) {
        $errors['jeux'] = 'Sélectionnez au moins un exercice.';
    }

    if (!empty($errors)) {
        $_SESSION['creation_test_errors'] = $errors;
        $_SESSION['creation_test_old']    = compact('titre', 'duree') + ['jeux' => $jeux];
        header('Location: ' . BASE_URL . '/admin?page=create_test');
        exit;
    }

    $id_test = creer_test_bdd($titre, (int)$duree, $_SESSION['id_admin']);
    lier_jeux_test($id_test, $jeux);
    $_SESSION['admin_success'] = 'Test créé avec succès.';
    header('Location: ' . BASE_URL . '/admin?page=tests');
    exit;
}

// POST /admin/attribution — Attribue un test à un candidat
function traiter_attribution_test()
{
    verifier_admin();

    $id_candidat = trim($_POST['candidat'] ?? '');
    $id_test     = trim($_POST['test']     ?? '');

    $errors = [];
    if ($id_candidat === '') {
        $errors['candidat'] = 'Sélectionnez un candidat.';
    }
    if ($id_test === '') {
        $errors['test'] = 'Sélectionnez une épreuve.';
    }

    if (!empty($errors)) {
        $_SESSION['attribution_test_errors'] = $errors;
        $_SESSION['attribution_test_old']    = ['candidat' => $id_candidat, 'test' => $id_test];
        header('Location: ' . BASE_URL . '/admin?page=attribution_test');
        exit;
    }

    attribuer_test_a_candidat((int)$id_test, (int)$id_candidat);
    $_SESSION['admin_success'] = 'Test attribué avec succès.';
    header('Location: ' . BASE_URL . '/admin?page=attribution_test');
    exit;
}
