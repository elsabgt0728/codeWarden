<?php

function traiter_creation_test()
{
    verifier_admin();

    require_once ROOT . '/app/models/test.php';
    require_once ROOT . '/app/models/session.php';
    require_once ROOT . '/app/models/convocation.php';

    // Données du formulaire
    $titre        = trim($_POST['titre_test']);
    $duree        = intval($_POST['duree']);          // minutes
    $jeux_ids     = $_POST['jeux']      ?? [];        // checkboxes
    $candidats_ids = $_POST['candidats'] ?? [];       // checkboxes

    $id_admin = $_SESSION['id_admin'];

    // 1) Créer le TEST
    $id_test = tests_creer($titre, $duree, 'actif', $id_admin);

    // 2) Associer les JEUX au TEST
    $position = 1;
    foreach ($jeux_ids as $id_jeux) {
        tests_ajouter_jeu($id_test, $id_jeux, $position, 1);
        $position++;
    }

    // 3) Créer une SESSION pour ce test
    $date_debut = date('Y-m-d H:i:s');
    $id_session = session_creer($id_test, $id_admin, $date_debut, null);

    // 4) Créer une CONVOCATION pour chaque candidat
    foreach ($candidats_ids as $id_candidat) {
        $lien = BASE_URL . "/candidat?page=jeu&id_test=" . $id_test . "&session=" . $id_session;
        convocation_creer($id_candidat, $id_session, $lien, null);
    }

    // 5) Redirection
    header("Location: " . BASE_URL . "/admin?page=tests&success=1");
    exit;
}

function candidat_commencer_test()
{
    verifier_candidat();

    require_once ROOT . '/app/models/test.php';
    require_once ROOT . '/app/models/passage_test.php';
    require_once ROOT . '/app/models/session.php';

    $id_test = $_GET['id_test'] ?? null;
    if (!$id_test) {
        die("Aucun test sélectionné.");
    }

    $user = trouver_candidat_par_id($_SESSION['id_candidat']);

    // Récupérer la session active du test
    $session = session_recuperer_par_test($id_test);
    if (!$session) {
        die("Aucune session active pour ce test.");
    }

    $id_session = $session['id_session'];

    // Vérifier si un passage existe déjà
    $passage = passage_test_en_cours($_SESSION['id_candidat'], $id_session);

    if (!$passage) {
        // Créer un nouveau passage
        $id_passage = creer_passage_test($_SESSION['id_candidat'], $id_session);
    } else {
        $id_passage = $passage['id_passage_test'];
    }

    // Charger les questions
    $test = test_recuperer_par_id($id_test);
    $jeux = test_recuperer_jeux($id_test);

    $questions = [];
    foreach ($jeux as $jeu) {
        $json = json_decode($jeu['contenu_json'], true);
        foreach ($json['questions'] as $q) {
            $questions[] = $q;
        }
    }

    afficher_vue('candidat/pageCandidat', [
        'questions' => $questions,
        'duree' => $test['duree_minutes'],
        'id_passage' => $id_passage,
        'id_test' => $id_test,
        'user' => $user
    ]);
}

