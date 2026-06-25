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

    require_once ROOT . '/app/models/test.php';

    // 1) Récupérer le test
    $test = test_recuperer_par_id($id_test);

    if (!$test) {
        die("Test introuvable.");
    }

    // 2) Récupérer les jeux du test
    $jeux = test_recuperer_jeux($id_test);

    // 3) Fusionner toutes les questions
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

    // 4) Envoyer à la vue
    afficher_vue('candidat/pageCandidat', [
        'questions' => $questions,
        'duree' => $test['duree_minutes'],
        "user" => $user
    ]);

    return;
    }

    if ($page === 'finish') {
     // PAGE FINISH
    afficher_vue("candidat/pageCandidat", [
    "user" => $user
]);
    }


    if ($page === 'dashboard') {
   afficher_vue("candidat/pageCandidat", [
    "user" => $user
]);

}
    }




// GET /profile — Affiche le profil
function page_profil()
{
    verifier_candidat();

    $user    = trouver_candidat_par_id($_SESSION['id_candidat']);
    $errors  = isset($_SESSION['profile_errors'])  ? $_SESSION['profile_errors']  : [];
    $success = isset($_SESSION['profile_success']) ? $_SESSION['profile_success'] : '';
    $form    = isset($_SESSION['profile_form'])    ? $_SESSION['profile_form']    : $user;
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


