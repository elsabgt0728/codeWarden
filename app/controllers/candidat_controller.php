<?php

require_once ROOT . "/app/models/test.php";


function page_candidat(){

    $page = $_GET['page'] ?? 'dashboard';

    verifier_candidat();
    $user = trouver_candidat_par_id($_SESSION["id_candidat"]);

    if ($page === 'test') {

        $id_test = $_GET['id_test'] ?? null;

        if (!$id_test) {
            die("Aucun test sélectionné.");
        }

        $test = test_recuperer_par_id($id_test);

        if (!$test) {
            die("Test introuvable.");
        }

        $jeux = test_recuperer_jeux($id_test);
        $questions = [];

        if (!empty($jeux)) {
            foreach ($jeux as $jeu) {
                if (!empty($jeu['contenu_json'])) {
                    $json = json_decode($jeu['contenu_json'], true);
                    if (!empty($json['questions'])) {
                        foreach ($json['questions'] as $q) {
                            $questions[] = $q;
                        }
                    }
                }
            }
        }

        afficher_vue('candidat/pageCandidat', [
            'questions' => $questions,
            'duree'     => $test['duree_minutes'],
            'user'      => $user,
        ]);
        return;
    }

    if ($page === 'finish') {
        candidat_afficher_finish();
        return;
    }

    if ($page === 'expire') {
        candidat_expire_test();
        exit;
    }

    // dashboard par défaut
    afficher_vue("candidat/pageCandidat", [
        "user" => $user,
    ]);
}


function candidat_terminer_test()
{
    verifier_candidat();

    require_once ROOT . '/app/models/passageTest.php';
    require_once ROOT . '/app/models/resultat.php';
    require_once ROOT . '/app/models/test.php';

    $id_passage = $_POST['id_passage'] ?? null;
    $id_test    = $_POST['id_test']    ?? null;
    $reponses   = $_POST['reponses']   ?? null;

    if (!$id_passage || !$id_test || !$reponses) {
        die("Requête invalide.");
    }

    $jeux = test_recuperer_jeux($id_test);
    $questions = [];

    foreach ($jeux as $jeu) {
        $json = json_decode($jeu['contenu_json'], true);
        foreach ($json['questions'] as $q) {
            $questions[] = $q;
        }
    }

    $score = 0;
    foreach ($questions as $i => $q) {
        if (isset($reponses[$i]) && $reponses[$i] == $q['bonne_reponse']) {
            $score++;
        }
    }

    terminer_passage_test($id_passage, $score);
    creer_resultat($id_passage, $score);

    header("Location: " . BASE_URL . "/candidat?page=finish&id_passage=$id_passage");
    exit;
}

function candidat_afficher_finish()
{
    verifier_candidat();

    require_once ROOT . '/app/models/passageTest.php';
    require_once ROOT . '/app/models/resultat.php';

    $id_passage = $_GET['id_passage'] ?? null;
    if (!$id_passage) {
        die("Passage introuvable.");
    }

    $user     = trouver_candidat_par_id($_SESSION['id_candidat']);
    $passage  = passage_test_par_id($id_passage);
    $resultat = resultat_par_passage($id_passage);

    afficher_vue('candidat/pageCandidat', [
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

    $data = json_decode(file_get_contents("php://input"), true);

    $id_passage = $data["id_passage"] ?? null;
    $id_test    = $data["id_test"]    ?? null;

    if (!$id_passage || !$id_test) {
        http_response_code(400);
        exit("Requête invalide.");
    }

    passage_test_expire($id_passage);
    creer_resultat($id_passage, 0);

    echo "OK";
    exit;
}


// GET /profile — Affiche le profil
function page_profil()
{
    verifier_candidat();

    $user    = trouver_candidat_par_id($_SESSION['id_candidat']);
    $errors  = $_SESSION['profile_errors']  ?? [];
    $success = $_SESSION['profile_success'] ?? '';
    $form    = $_SESSION['profile_form']    ?? $user;
    unset($_SESSION['profile_errors'], $_SESSION['profile_success'], $_SESSION['profile_form']);

    afficher_vue('candidat/profile', [
        'user'    => $user,
        'errors'  => $errors,
        'success' => $success,
        'form'    => $form,
    ]);
}

// POST /profile — Enregistre les modifications du profil
function modifier_profil()
{
    verifier_candidat();

    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
              strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    $prenom = trim($_POST['prenom'] ?? '');
    $nom    = trim($_POST['nom']    ?? '');
    $email  = trim($_POST['email']  ?? '');
    $userId = $_SESSION['id_candidat'];

    $errors = [];

    if ($prenom === '') $errors['prenom'] = 'Le prénom est obligatoire.';
    if ($nom    === '') $errors['nom']    = 'Le nom est obligatoire.';

    if ($email === '') {
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
        $_SESSION['profile_form']   = ['prenom' => $prenom, 'nom' => $nom, 'email' => $email];
        header('Location: ' . BASE_URL . '/profile');
        exit;
    }

    modifier_candidat($userId, $nom, $prenom, $email);

    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode([
            'success'     => true,
            'nom_complet' => trim("$prenom $nom"),
        ]);
        exit;
    }

    $_SESSION['profile_success'] = 'Profil mis à jour.';
    header('Location: ' . BASE_URL . '/profile');
    exit;
}
