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
