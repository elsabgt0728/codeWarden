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
    verifier_csrf();

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
    $_SESSION['id_admin']         = $adminUser['id_admin'];
    $_SESSION['admin_role']       = $adminUser['role'];
    // Nécessaire pour cloisonner toutes les requêtes admin (candidats, jeux,
    // stats, résultats, groupes) à son propre établissement.
    $_SESSION['id_etablissement'] = $adminUser['id_etablissement'];

    header('Location: ' . BASE_URL . '/admin');
    exit;
}

function tableau_de_bord()
{
    verifier_admin();

    $page             = $_GET['page'] ?? 'exercices';
    $id_admin         = $_SESSION['id_admin'];
    $id_etablissement = $_SESSION['id_etablissement'];

    $exercices = lister_jeux_admin($id_admin);
    $tests     = lister_test_admin($id_admin);

    $exercice_selectionne = null;
    if (isset($_GET['exercice_id'])) {
        $id_cible = (int) $_GET['exercice_id'];
        foreach ($exercices as $ex) {
            if ((int)$ex['id_jeux'] === $id_cible) { $exercice_selectionne = $ex; break; }
        }
    }

    $test_selectionne  = null;
    $jeux_test_selectionne = [];
    $candidats_test_selectionne = [];
    if (isset($_GET['test_id'])) {
        $id_cible = (int) $_GET['test_id'];
        foreach ($tests as $t) {
            if ((int)$t['id_test'] === $id_cible) { $test_selectionne = $t; break; }
        }
        if ($test_selectionne) {
            require_once ROOT . '/app/models/test.php';
            require_once ROOT . '/app/models/convocation.php';
            $jeux_test_selectionne      = test_recuperer_jeux($id_cible);
            $candidats_test_selectionne = convocations_par_test($id_cible);
        }
    }

    $base = [
        'page'                       => $page,
        'exercices'                  => $exercices,
        'tests'                      => $tests,
        'exercice_selectionne'       => $exercice_selectionne,
        'test_selectionne'           => $test_selectionne,
        'jeux_test_selectionne'      => $jeux_test_selectionne,
        'candidats_test_selectionne' => $candidats_test_selectionne,
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
        require_once ROOT . '/app/models/groupe.php';
        afficher_vue('admin/dashboard', $base + [
            'jeux'      => jeux_tous_actifs($id_etablissement),
            'candidats' => candidats_tous_actifs($id_etablissement),
            'groupes'   => lister_groupes_etablissement($id_etablissement),
        ]);
        return;
    }

    if ($page === 'statistiques') {
        require_once ROOT . '/app/models/stats.php';
        afficher_vue('admin/dashboard', $base + [
            'stats_globales' => stats_admin_global([], $id_etablissement),
            'stats_tests'    => stats_admin_comparaison([], $id_etablissement),
        ]);
        return;
    }

    if ($page === 'etudiants') {
        require_once ROOT . '/app/models/resultat.php';
        afficher_vue('admin/dashboard', $base + [
            'candidats_liste' => resultats_candidats_liste($id_etablissement),
        ]);
        return;
    }

    if ($page === 'groupes') {
        require_once ROOT . '/app/models/groupe.php';
        require_once ROOT . '/app/models/candidat.php';

        $groupes = lister_groupes_etablissement($id_etablissement);

        // Détail d'un groupe sélectionné (?groupe_id=X) : ses membres + les
        // candidats actifs de l'établissement qui n'en font pas encore partie
        $groupe_selectionne    = null;
        $membres_groupe        = [];
        $candidats_disponibles = [];
        if (isset($_GET['groupe_id'])) {
            $groupe_selectionne = groupe_par_id((int)$_GET['groupe_id'], $id_etablissement);
            if ($groupe_selectionne) {
                $membres_groupe = groupe_candidats($groupe_selectionne['id_groupe']);
                $ids_membres    = array_column($membres_groupe, 'id_candidat');
                $candidats_disponibles = array_values(array_filter(
                    candidats_tous_actifs($id_etablissement),
                    fn($c) => !in_array((int)$c['id_candidat'], $ids_membres)
                ));
            }
        }

        afficher_vue('admin/dashboard', $base + [
            'groupes'               => $groupes,
            'groupe_selectionne'    => $groupe_selectionne,
            'membres_groupe'        => $membres_groupe,
            'candidats_disponibles' => $candidats_disponibles,
        ]);
        return;
    }

    afficher_vue('admin/dashboard', $base);
}

function admin_decision_candidat()
{
    verifier_admin();
    verifier_csrf();
    require_once ROOT . '/app/models/resultat.php';
    require_once ROOT . '/app/models/passageTest.php';
    require_once ROOT . '/app/helpers/mailer.php';

    $id_passage = (int)($_POST['id_passage_test'] ?? 0);
    $decision   = $_POST['decision'] ?? '';
    $valides    = ['admis', 'refuse', 'liste_attente', 'en_attente'];

    if ($id_passage && in_array($decision, $valides)) {
        // Bloquer si une décision finale a déjà été prise
        $resultat_existant = resultat_par_passage($id_passage);
        if ($resultat_existant && in_array($resultat_existant['decision'], ['admis', 'refuse', 'liste_attente'])) {
            header('Location: ' . BASE_URL . '/admin?page=etudiants');
            exit;
        }

        resultat_mettre_a_jour_decision($id_passage, $decision);

        $infos = passage_candidat_et_test($id_passage);
        if ($infos) {
            $ok = envoyer_decision(
                $infos['email'],
                $infos['prenom'],
                $infos['nom'],
                $infos['titre_test'],
                $decision
            );
            if (!$ok) {
                error_log('[CodeWarden] envoyer_decision a échoué pour id_passage=' . $id_passage . ' email=' . $infos['email']);
                header('Location: ' . BASE_URL . '/admin?page=etudiants&mail_error=1');
                exit;
            }
        } else {
            error_log('[CodeWarden] passage_candidat_et_test a retourné false pour id_passage=' . $id_passage);
            header('Location: ' . BASE_URL . '/admin?page=etudiants&infos_error=1');
            exit;
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
