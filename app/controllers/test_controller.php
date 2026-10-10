<?php

function traiter_creation_test()
{
    verifier_admin();
    verifier_csrf();

    require_once ROOT . '/app/models/test.php';
    require_once ROOT . '/app/models/session.php';
    require_once ROOT . '/app/models/convocation.php';
    require_once ROOT . '/app/models/jeux.php';
    require_once ROOT . '/app/models/candidat.php';
    require_once ROOT . '/app/models/groupe.php';

    $id_admin         = $_SESSION['id_admin'];
    $id_etablissement = $_SESSION['id_etablissement'];

    // Données du formulaire
    $titre          = trim($_POST['titre_test'] ?? '');
    $duree          = intval($_POST['duree'] ?? 0);
    $jeux_ids       = $_POST['jeux']      ?? [];
    $candidats_ids  = $_POST['candidats'] ?? [];
    $groupes_ids    = $_POST['groupes']   ?? [];

    if ($titre === '' || $duree <= 0) {
        header('Location: ' . BASE_URL . '/admin?page=creer_test&error=champs_manquants');
        exit;
    }

    // Candidats sélectionnés individuellement + candidats résolus depuis les
    // groupes cochés, fusionnés et dédupliqués (un candidat peut être dans
    // un groupe convoqué ET coché individuellement, on ne le convoque qu'une fois)
    $candidats_ids = array_values(array_unique(array_merge(
        array_map('intval', $candidats_ids),
        candidats_par_groupes($groupes_ids, $id_etablissement)
    )));

    // Sécurité : on ne fait pas confiance aux IDs reçus en POST, on ne garde
    // que ceux qui appartiennent réellement à l'établissement de l'admin
    // (même si l'UI ne propose normalement que les bons éléments).
    $jeux_ids      = jeux_filtrer_par_etablissement($jeux_ids, $id_etablissement);
    $candidats_ids = candidats_filtrer_par_etablissement($candidats_ids, $id_etablissement);

    if (empty($jeux_ids)) {
        header('Location: ' . BASE_URL . '/admin?page=creer_test&error=aucun_jeu');
        exit;
    }

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
    verifier_csrf();

    require_once ROOT . '/app/models/convocation.php';
    require_once ROOT . '/app/models/test.php';
    require_once ROOT . '/app/helpers/mailer.php';

    $id_test = (int)($_POST['id_test'] ?? 0);
    if (!$id_test) { header('Location: ' . BASE_URL . '/admin?page=tests'); exit; }

    $test         = test_recuperer_par_id($id_test);
    $convocations = convocations_par_test($id_test);

    // Bloquer si déjà convoqué
    if (test_est_convoque($id_test)) {
        error_log('[CodeWarden] Convoquer bloqué (déjà convoqué) pour id_test=' . $id_test);
        header('Location: ' . BASE_URL . '/admin?page=tests&error=deja_convoque');
        exit;
    }

    if (empty($convocations)) {
        error_log('[CodeWarden] Convoquer annulé : aucun candidat pour id_test=' . $id_test);
        header('Location: ' . BASE_URL . '/admin?page=tests&error=aucun_candidat');
        exit;
    }

    // Fixer l'expiration à 7 jours à partir de maintenant
    convocations_fixer_expiration($id_test, 7);
    $date_expiration = date('d/m/Y', strtotime('+7 days'));

    // URL absolue obligatoire pour les liens dans les emails
    $protocole = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $base_lien = $protocole . '://' . $_SERVER['HTTP_HOST'] . BASE_URL . '/candidat/commencer?token=';

    // Sécurité : chaque candidat reçoit un lien PERSONNEL contenant son propre
    // jeton (conv['token'], généré par convocation_creer), et non plus un lien
    // partagé "?id_test=X" valable pour n'importe quel compte connecté.
    foreach ($convocations as &$conv) {
        $conv['lien'] = $base_lien . $conv['token'];
    }
    unset($conv);

    // Un seul lot = une seule connexion SMTP réutilisée pour tous les candidats,
    // au lieu d'en ouvrir une par candidat (cf. mailer.php)
    $resultat = envoyer_convocations_lot(
        $convocations,
        $test['titre'],
        $test['duree_minutes'],
        $date_expiration
    );

    $nb_ok = count($resultat['ok']);

    if (!empty($resultat['echecs'])) {
        error_log('[CodeWarden] Échec envoi convocation pour ' . implode(', ', $resultat['echecs']) . ' (id_test=' . $id_test . ')');
    }

    $url = BASE_URL . '/admin?page=tests&success=convoque&nb=' . $nb_ok;
    if (!empty($resultat['echecs'])) {
        $url .= '&nb_echecs=' . count($resultat['echecs']);
    }

    header('Location: ' . $url);
    exit;
}

function page_modifier_test()
{
    verifier_admin();
    require_once ROOT . '/app/models/test.php';
    require_once ROOT . '/app/models/admin.php';
    require_once ROOT . '/app/models/jeux.php';
    require_once ROOT . '/app/models/candidat.php';
    require_once ROOT . '/app/models/convocation.php';
    require_once ROOT . '/app/models/session.php';
    require_once ROOT . '/app/models/groupe.php';

    $id   = (int)($_GET['id'] ?? 0);
    $test = $id ? test_recuperer_par_id($id) : null;
    if (!$test) { header('Location: ' . BASE_URL . '/admin?page=tests'); exit; }

    $id_admin         = $_SESSION['id_admin'];
    $id_etablissement = $_SESSION['id_etablissement'];

    // Jeux actuellement assignés à ce test
    $jeux_assignes    = test_recuperer_jeux($id);
    $ids_jeux_assignes = array_column($jeux_assignes, 'id_jeux');

    // Candidats actuellement convoqués pour ce test
    $candidats_assignes    = convocations_par_test($id);
    $ids_candidats_assignes = array_column($candidats_assignes, 'id_candidat');

    // Est-ce que le test a déjà été convoqué ?
    $est_convoque = test_est_convoque($id);

    afficher_vue('admin/dashboard', [
        'page'                  => 'modifier_test',
        'exercices'             => lister_jeux_admin($id_admin),
        'tests'                 => lister_test_admin($id_admin),
        'exercice_selectionne'  => null,
        'test_selectionne'      => null,
        'test_edition'          => $test,
        'tous_les_jeux'         => jeux_tous_actifs($id_etablissement),
        'tous_les_candidats'    => candidats_tous_actifs($id_etablissement),
        'groupes'               => lister_groupes_etablissement($id_etablissement),
        'ids_jeux_assignes'     => $ids_jeux_assignes,
        'ids_candidats_assignes'=> $ids_candidats_assignes,
        'est_convoque'          => $est_convoque,
    ]);
}

function traiter_modification_test()
{
    verifier_admin();
    verifier_csrf();
    require_once ROOT . '/app/models/test.php';
    require_once ROOT . '/app/models/session.php';
    require_once ROOT . '/app/models/convocation.php';
    require_once ROOT . '/app/models/jeux.php';
    require_once ROOT . '/app/models/candidat.php';
    require_once ROOT . '/app/models/groupe.php';

    $id_etablissement = $_SESSION['id_etablissement'];

    $id        = (int)($_POST['id_test']   ?? 0);
    $titre     = trim($_POST['titre_test'] ?? '');
    $duree     = max(1, intval($_POST['duree'] ?? 60));
    $statut    = $_POST['statut']          ?? 'actif';
    $jeux_ids  = $_POST['jeux']            ?? [];
    $cands_ids = $_POST['candidats']       ?? [];
    $groupes_ids = $_POST['groupes']       ?? [];

    if (!$id || $titre === '') {
        header('Location: ' . BASE_URL . '/admin/test/modifier?id=' . $id . '&error=1');
        exit;
    }

    // Même logique de fusion groupes + candidats individuels qu'à la création
    $cands_ids = array_values(array_unique(array_merge(
        array_map('intval', $cands_ids),
        candidats_par_groupes($groupes_ids, $id_etablissement)
    )));

    // Sécurité : revalidation serveur des IDs reçus (cf. traiter_creation_test)
    $jeux_ids  = jeux_filtrer_par_etablissement($jeux_ids, $id_etablissement);
    $cands_ids = candidats_filtrer_par_etablissement($cands_ids, $id_etablissement);

    // Mettre à jour titre/durée/statut
    test_mettre_a_jour($id, $titre, $duree, $statut);

    // Mettre à jour les jeux assignés
    test_mettre_a_jour_jeux($id, $jeux_ids);

    // Mettre à jour les candidats (seulement si pas encore convoqué)
    if (!test_est_convoque($id)) {
        $session = session_recuperer_par_test($id);
        if ($session) {
            $lien = BASE_URL . '/candidat/commencer?id_test=' . $id;
            $db   = connecter_bdd();
            $db->prepare("DELETE FROM convocation WHERE id_session = ?")->execute([$session['id_session']]);
            foreach ($cands_ids as $id_candidat) {
                convocation_creer((int)$id_candidat, (int)$session['id_session'], $lien, null);
            }
        }
    }

    header('Location: ' . BASE_URL . '/admin?page=tests&success=modifie');
    exit;
}

function traiter_suppression_test()
{
    verifier_admin();
    verifier_csrf();
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
    require_once ROOT . '/app/models/convocation.php';

    // Le lien de convocation est désormais personnel : il porte un jeton
    // propre à CE candidat, pas juste un id_test valable pour qui que ce soit.
    $token = $_GET['token'] ?? null;
    if (!$token) die("Lien de convocation invalide.");

    $convocation = convocation_par_token($token);
    if (!$convocation) {
        http_response_code(404);
        die("Lien de convocation invalide ou introuvable.");
    }

    // Sécurité : ce lien n'est valable que pour le candidat auquel il a été
    // envoyé — pas pour n'importe quel compte connecté au moment du clic.
    if ((int)$convocation['id_candidat'] !== (int)$_SESSION['id_candidat']) {
        http_response_code(403);
        die("Ce lien de convocation est associé à un autre compte candidat. Déconnectez-vous puis reconnectez-vous avec le compte auquel ce lien a été envoyé.");
    }

    if (!empty($convocation['date_expiration']) && strtotime($convocation['date_expiration']) < time()) {
        http_response_code(403);
        die("Ce lien de convocation a expiré.");
    }

    $id_test = (int) $convocation['id_test'];

    $test = test_recuperer_par_id($id_test);
    if (!$test) die("Test introuvable.");

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
