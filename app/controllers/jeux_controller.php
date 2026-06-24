<?php

// ROUTAGE DIRECT SI APPELÉ PAR LE ROUTEUR
if (isset($_GET['action']) && $_GET['action'] === 'creer') {
    traiter_creation_jeux();
    exit;
}


function traiter_creation_jeux()
{
    verifier_admin();

    // Champs du jeu
    $titre       = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $bareme      = intval($_POST['bareme']);
    $difficulte  = $_POST['difficulte'];
    $type        = $_POST['categorie'];
    $duree       = intval($_POST['duree']);
    $statut      = $_POST['statut'];

    // QUESTIONS
    $questions_form = $_POST['questions'] ?? [];
    $questions_json = [];

    // Dossier upload
    $uploadDir = ROOT . "/public/uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    foreach ($questions_form as $qIndex => $q) {

        $intitule = trim($q['intitule']);
        $points   = intval($q['points']);
        $intrus   = intval($q['intrus']);

        $propositions = [];

        foreach ($q['propositions'] as $pIndex => $prop) {

            $label = trim($prop['label']);

            $imagePath = null;

            if (isset($_FILES['questions']['name'][$qIndex]['propositions'][$pIndex]['image']) &&
                $_FILES['questions']['error'][$qIndex]['propositions'][$pIndex]['image'] === UPLOAD_ERR_OK) {

                $tmpName = $_FILES['questions']['tmp_name'][$qIndex]['propositions'][$pIndex]['image'];
                $originalName = $_FILES['questions']['name'][$qIndex]['propositions'][$pIndex]['image'];

                $ext = pathinfo($originalName, PATHINFO_EXTENSION);
                $newName = "jeu_" . time() . "_q{$qIndex}_p{$pIndex}." . $ext;

                $imagePath = "uploads/" . $newName;

                move_uploaded_file($tmpName, $uploadDir . $newName);
            }

            $typeProp = $imagePath ? "image" : "texte";

            $propositions[] = [
                "type" => $typeProp,
                "label" => $label,
                "src" => $imagePath
            ];
        }

        $questions_json[] = [
            "intitule"      => $intitule,
            "propositions"  => $propositions,
            "intrus_index"  => $intrus,
            "points"        => $points
        ];
    }

    $contenu_json = json_encode([
        "description" => $description,
        "duree"       => $duree,
        "nbquestions" => count($questions_json),
        "questions"   => $questions_json
    ], JSON_UNESCAPED_UNICODE);

    $id_admin = $_SESSION['id_admin'];

    require_once ROOT . '/app/models/jeux.php';
    $ok = jeux_inserer($titre, $type, $difficulte, $bareme, $contenu_json, $statut, $id_admin);

    if ($ok) {
        header("Location: " . BASE_URL . "/admin?page=exercices&success=1");
        exit;
    } else {
        header("Location: " . BASE_URL . "/admin?page=creer_exercice&error=1");
        exit;
    }
}
