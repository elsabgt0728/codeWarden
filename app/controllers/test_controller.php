<?php

function traiter_creation_test()
{
    verifier_admin();

    require_once ROOT . '/app/models/test.php';
    require_once ROOT . '/app/models/session.php';
    require_once ROOT . '/app/models/convocation.php';

    // Données du formulaire
    $titre         = trim($_POST['titre_test']);
    $duree         = intval($_POST['duree']);
    $jeux_ids      = $_POST['jeux']      ?? [];
    $candidats_ids = $_POST['candidats'] ?? [];

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

    // 4) Créer une CONVOCATION pour chaque candidat (sans envoyer de mail ici)
    foreach ($candidats_ids as $id_candidat) {
        $lien = BASE_URL . '/candidat/commencer?id_test=' . $id_test;
        convocation_creer((int)$id_candidat, $id_session, $lien, null);
    }

    // 5) Redirection
    header("Location: " . BASE_URL . "/admin?page=tests&success=1");
    exit;
}

function admin_convoquer_test()
{
    verifier_admin();

    require_once ROOT . '/app/models/convocation.php';
    require_once ROOT . '/app/models/test.php';
    require_once ROOT . '/app/helpers/mailer.php';

    $id_test = (int)($_POST['id_test'] ?? 0);
    if (!$id_test) { header('Location: ' . BASE_URL . '/admin?page=tests'); exit; }

    $test         = test_recuperer_par_id($id_test);
    $convocations = convocations_par_test($id_test);

    // Bloquer si déjà convoqué
    if (test_est_convoque($id_test)) {
        header('Location: ' . BASE_URL . '/admin?page=tests&error=deja_convoque');
        exit;
    }

    // Fixer l'expiration à 7 jours à partir de maintenant
    convocations_fixer_expiration($id_test, 7);
    $date_expiration = date('d/m/Y', strtotime('+7 days'));

    // URL absolue obligatoire pour les liens dans les emails
    $protocole  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $lien_email = $protocole . '://' . $_SERVER['HTTP_HOST'] . BASE_URL . '/candidat/commencer?id_test=' . $id_test;

    foreach ($convocations as $conv) {
        envoyer_convocation(
            $conv['email'],
            $conv['prenom'],
            $conv['nom'],
            $test['titre'],
            $test['duree_minutes'],
            $lien_email,
            $date_expiration
        );
    }

    header('Location: ' . BASE_URL . '/admin?page=tests&success=convoque');
    exit;
}

function page_modifier_test()
{
    verifier_admin();
    require_once ROOT . '/app/models/test.php';
    require_once ROOT . '/app/models/admin.php';

    $id   = (int)($_GET['id'] ?? 0);
    $test = $id ? test_recuperer_par_id($id) : null;
    if (!$test) { header('Location: ' . BASE_URL . '/admin?page=tests'); exit; }

    $id_admin = $_SESSION['id_admin'];
    afficher_vue('admin/dashboard', [
        'page'                 => 'modifier_test',
        'exercices'            => lister_jeux_admin($id_admin),
        'tests'                => lister_test_admin($id_admin),
        'exercice_selectionne' => null,
        'test_selectionne'     => null,
        'test_edition'         => $test,
    ]);
}

function traiter_modification_test()
{
    verifier_admin();
    require_once ROOT . '/app/models/test.php';

    $id    = (int)($_POST['id_test']    ?? 0);
    $titre = trim($_POST['titre_test']  ?? '');
    $duree = max(1, intval($_POST['duree'] ?? 60));
    $statut = $_POST['statut']          ?? 'actif';

    if (!$id || $titre === '') {
        header('Location: ' . BASE_URL . '/admin/test/modifier?id=' . $id . '&error=1');
        exit;
    }

    test_mettre_a_jour($id, $titre, $duree, $statut);
    header('Location: ' . BASE_URL . '/admin?page=tests&success=modifie');
    exit;
}

function traiter_suppression_test()
{
    verifier_admin();
    require_once ROOT . '/app/models/test.php';

    $id = (int)($_POST['id_test'] ?? 0);
    if ($id) test_supprimer($id);
    header('Location: ' . BASE_URL . '/admin?page=tests&success=supprime');
    exit;
}

function candidat_commencer_test()
{
    verifier_candidat();

    require_once ROOT . '/app/models/test.php';
    require_once ROOT . '/app/models/passageTest.php';
    require_once ROOT . '/app/models/session.php';

    $id_test = $_GET['id_test'] ?? null;
    if (!$id_test) die("Aucun test sélectionné.");

    $test = test_recuperer_par_id($id_test);
    if (!$test) die("Test introuvable.");

    // Vérifier que ce candidat est bien convoqué pour ce test
    require_once ROOT . '/app/models/convocation.php';
    if (!candidat_est_convoque((int)$_SESSION['id_candidat'], (int)$id_test)) {
        http_response_code(403);
        die("Accès refusé : vous n'êtes pas convoqué(e) pour ce test.");
    }

    // Vérifier que le candidat n'a pas déjà passé ce test
    if (candidat_a_deja_passe((int)$_SESSION['id_candidat'], (int)$id_test)) {
        die("Vous avez déjà passé ce test. Consultez votre espace candidat pour voir vos résultats.");
    }

    // Récupérer tous les jeux HTML du test (dans l'ordre)
    $jeux_bruts = test_recuperer_jeux($id_test);
    $jeux_liste = [];
    foreach ($jeux_bruts as $jeu) {
        if (!empty($jeu['contenu_html'])) {
            $jeux_liste[] = [
                'id_jeux'      => (int)$jeu['id_jeux'],
                'titre'        => $jeu['titre'],
                'bareme'       => (int)$jeu['bareme'],
                'contenu_html' => $jeu['contenu_html'],
            ];
        }
    }
    if (empty($jeux_liste)) die("Aucun jeu HTML disponible pour ce test.");

    // Session active du test
    $session = session_recuperer_par_test($id_test);
    if (!$session) die("Aucune session active pour ce test.");

    // Créer ou récupérer le passage en cours
    $passage_existant = passage_test_en_cours((int) $_SESSION['id_candidat']);
    if ($passage_existant) {
        $id_passage = (int) $passage_existant['id_passage_test'];
    } else {
        $passage_existant = null;
        $id_passage = (int) creer_passage_test((int) $_SESSION['id_candidat'], (int) $session['id_session']);
        $passage_existant = passage_test_en_cours((int) $_SESSION['id_candidat']);
    }

    // Temps déjà écoulé (pour que le timer ne reparte pas à zéro au refresh)
    $temps_ecoule = 0;
    if ($passage_existant && !empty($passage_existant['date_debut'])) {
        $temps_ecoule = max(0, time() - strtotime($passage_existant['date_debut']));
    }

    // Jeux déjà joués (pour restaurer la progression après refresh)
    require_once ROOT . '/app/models/resultat.php';
    $scores_existants = reponses_scores_par_passage($id_passage);

    $user = trouver_candidat_par_id($_SESSION['id_candidat']);

    afficher_vue('candidat/pageCandidat', [
        'page'             => 'test',
        'jeux_liste'       => $jeux_liste,
        'duree'            => (int) $test['duree_minutes'],
        'user'             => $user,
        'id_passage'       => $id_passage,
        'id_test'          => (int) $id_test,
        'temps_ecoule'     => $temps_ecoule,
        'scores_existants' => $scores_existants,
    ]);
}

