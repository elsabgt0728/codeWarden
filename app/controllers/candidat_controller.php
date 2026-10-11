<?php

require_once ROOT . "/app/models/test.php";

function page_candidat()
{
    $page = $_GET['page'] ?? 'dashboard';
    verifier_candidat();
    $user = trouver_candidat_par_id($_SESSION['id_candidat']);

    // NB : le passage d'un test se fait exclusivement via le lien personnel
    // /candidat/commencer?token=... (candidat_commencer_test() dans
    // test_controller.php, vérifié par jeton propre à chaque candidat).
    // Il n'y a pas de route ?page=test ici : le bouton "Commencer" du
    // tableau de bord pointe directement vers ce lien personnel.

    if ($page === 'finish') {
        candidat_afficher_finish();
        return;
    }

    if ($page === 'expire') {
        candidat_expire_test();
        exit;
    }

    // dashboard par défaut
    require_once ROOT . '/app/models/convocation.php';
    require_once ROOT . '/app/models/passageTest.php';

    $test_info       = convocation_candidat((int) $_SESSION['id_candidat']);
    $passage_termine = passage_test_termine_avec_resultat((int) $_SESSION['id_candidat']);

    afficher_vue('candidat/pageCandidat', [
        'page'             => 'dashboard',
        'user'             => $user,
        'test_info'        => $test_info,
        'passage_termine'  => $passage_termine,
    ]);
}

function candidat_enregistrer_score_jeu()
{
    verifier_candidat();
    require_once ROOT . '/app/models/resultat.php';
    require_once ROOT . '/app/models/passageTest.php';

    $data       = json_decode(file_get_contents('php://input'), true) ?? [];
    $id_passage = (int)($data['id_passage'] ?? 0);
    $id_jeu     = (int)($data['id_jeu']     ?? 0);
    $score      = (float)($data['score']    ?? 0);

    if (!$id_passage || !$id_jeu) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['erreur' => 'Requête invalide']);
        exit;
    }

    // Sécurité : empêche d'enregistrer un score sur le passage d'un AUTRE
    // candidat (onglet resté ouvert sur un autre compte, id_passage deviné...)
    if (!passage_appartient_candidat($id_passage, (int)$_SESSION['id_candidat'])) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erreur' => 'Non autorisé']);
        exit;
    }

    // Le score est borné [0,100] directement dans enregistrer_score_jeu()
    enregistrer_score_jeu($id_passage, $id_jeu, $score);

    header('Content-Type: application/json');
    echo json_encode(['ok' => true]);
    exit;
}

function candidat_terminer_test()
{
    verifier_candidat();

    require_once ROOT . '/app/models/passageTest.php';
    require_once ROOT . '/app/models/resultat.php';

    $data = json_decode(file_get_contents('php://input'), true) ?? [];

    $id_passage  = $data['id_passage']  ?? null;
    $id_test     = $data['id_test']     ?? null;
    $scores_jeux = $data['scores_jeux'] ?? [];

    if (!$id_passage || !$id_test) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['erreur' => 'Requête invalide']);
        exit;
    }

    // Sécurité : empêche de terminer/scorer le passage d'un AUTRE candidat
    if (!passage_appartient_candidat($id_passage, (int)$_SESSION['id_candidat'])) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erreur' => 'Non autorisé']);
        exit;
    }

    // Filet de sécurité contre une course possible entre l'appel asynchrone
    // /candidat/score-jeu (envoyé par le navigateur à chaque jeu terminé,
    // sans être attendu avant d'enchaîner) et cet appel /candidat/terminer :
    // si le dernier score n'a pas encore atteint la base, on le réenregistre
    // ici à partir de ce que le client a déjà en mémoire. Sans danger à
    // rejouer : enregistrer_score_jeu() remplace (delete+insert) et borne
    // toujours le score à [0,100] — ce n'est pas une nouvelle source de
    // confiance, juste une garantie d'ordre d'écriture.
    foreach ($scores_jeux as $sg) {
        $id_jeu    = (int)($sg['id_jeu'] ?? 0);
        $score_jeu = (float)($sg['score'] ?? 0);
        if ($id_jeu > 0) {
            enregistrer_score_jeu((int)$id_passage, $id_jeu, $score_jeu);
        }
    }

    // Sécurité : le score final n'est plus repris du JSON envoyé par le
    // client (facilement falsifiable via un appel direct à cette route).
    // On le recalcule côté serveur à partir des scores par jeu désormais
    // garantis à jour en base (cf. filet de sécurité ci-dessus).
    $score = calculer_score_total_passage((int)$id_passage, (int)$id_test);

    terminer_passage_test($id_passage, $score);

    $existant = resultat_par_passage($id_passage);
    if (!$existant) creer_resultat($id_passage, $score);

    header('Content-Type: application/json');
    echo json_encode([
        'redirect' => BASE_URL . '/candidat?page=finish&id_passage=' . $id_passage
    ]);
    exit;
}

function candidat_afficher_finish()
{
    verifier_candidat();

    require_once ROOT . '/app/models/passageTest.php';
    require_once ROOT . '/app/models/resultat.php';

    $id_passage = $_GET['id_passage'] ?? null;
    if (!$id_passage) die("Passage introuvable.");

    // Sécurité : empêche de consulter le résultat d'un AUTRE candidat en
    // devinant/modifiant l'id_passage dans l'URL
    if (!passage_appartient_candidat($id_passage, (int)$_SESSION['id_candidat'])) {
        http_response_code(403);
        die("Accès refusé : ce résultat ne vous appartient pas.");
    }

    $user     = trouver_candidat_par_id($_SESSION['id_candidat']);
    $passage  = passage_test_par_id($id_passage);
    $resultat = resultat_par_passage($id_passage);

    afficher_vue('candidat/pageCandidat', [
        'page'     => 'finish',
        'user'     => $user,
        'passage'  => $passage,
        'resultat' => $resultat,
    ]);
}

function candidat_expire_test()
{
    verifier_candidat();

    require_once ROOT . '/app/models/passageTest.php';
    require_once ROOT . '/app/models/resultat.php';

    $data = json_decode(file_get_contents('php://input'), true);

    $id_passage = $data['id_passage'] ?? null;
    $id_test    = $data['id_test']    ?? null;

    if (!$id_passage || !$id_test) {
        http_response_code(400);
        exit('Requête invalide.');
    }

    // Sécurité : empêche de faire expirer le passage d'un AUTRE candidat
    if (!passage_appartient_candidat($id_passage, (int)$_SESSION['id_candidat'])) {
        http_response_code(403);
        exit('Non autorisé.');
    }

    passage_test_expire($id_passage);

    $existant = resultat_par_passage($id_passage);
    if (!$existant) creer_resultat($id_passage, 0);

    http_response_code(200);
    echo 'OK';
    exit;
}

function page_profil()
{
    verifier_candidat();
    $user    = trouver_candidat_par_id($_SESSION['id_candidat']);
    $errors  = $_SESSION['profile_errors']  ?? [];
    $success = $_SESSION['profile_success'] ?? '';
    $form    = $_SESSION['profile_form']    ?? $user;
    unset($_SESSION['profile_errors'], $_SESSION['profile_success'], $_SESSION['profile_form']);
    afficher_vue('candidat/profile', compact('user', 'errors', 'success', 'form'));
}

function modifier_profil()
{
    verifier_candidat();
    verifier_csrf();

    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
              strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    $prenom = trim($_POST['prenom'] ?? '');
    $nom    = trim($_POST['nom']    ?? '');
    $email  = trim($_POST['email']  ?? '');
    $userId = $_SESSION['id_candidat'];
    $errors = [];

    if ($prenom === '') $errors['prenom'] = 'Le prénom est obligatoire.';
    if ($nom    === '') $errors['nom']    = 'Le nom est obligatoire.';
    if ($email  === '') {
        $errors['email'] = "L'e-mail est obligatoire.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Adresse e-mail invalide.';
    } elseif (email_deja_pris($email, $userId)) {
        $errors['email'] = 'Cet e-mail est déjà utilisé.';
    }

    if (!empty($errors)) {
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'errors' => $errors]);
            exit;
        }
        $_SESSION['profile_errors'] = $errors;
        $_SESSION['profile_form']   = compact('prenom', 'nom', 'email');
        header('Location: ' . BASE_URL . '/profile');
        exit;
    }

    modifier_candidat($userId, $nom, $prenom, $email);

    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'nom_complet' => trim("$prenom $nom")]);
        exit;
    }

    $_SESSION['profile_success'] = 'Profil mis à jour.';
    header('Location: ' . BASE_URL . '/profile');
    exit;
}
