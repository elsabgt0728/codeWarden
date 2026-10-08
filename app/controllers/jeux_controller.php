<?php

function traiter_creation_jeux()
{
    verifier_admin();
    verifier_csrf();

    require_once ROOT . '/app/models/jeux.php';

    $titre        = trim($_POST['titre']       ?? '');
    $bareme       = max(1, intval($_POST['bareme'] ?? 1));
    $difficulte   = $_POST['difficulte']       ?? 'moyen';
    $type         = $_POST['categorie']        ?? 'autre';
    $statut       = $_POST['statut']           ?? 'brouillon';
    $contenu_html = trim($_POST['contenu_html'] ?? '');

    if ($titre === '' || $contenu_html === '') {
        header('Location: ' . BASE_URL . '/admin?page=creer_exercice&error=champs_manquants');
        exit;
    }

    $id_admin = $_SESSION['id_admin'];
    $ok = jeux_inserer_html($titre, $type, $difficulte, $bareme, $statut, $contenu_html, $id_admin);

    if ($ok) {
        header('Location: ' . BASE_URL . '/admin?page=exercices&success=1');
    } else {
        header('Location: ' . BASE_URL . '/admin?page=creer_exercice&error=1');
    }
    exit;
}

function page_modifier_jeux()
{
    verifier_admin();
    require_once ROOT . '/app/models/jeux.php';
    require_once ROOT . '/app/models/admin.php';

    $id  = (int)($_GET['id'] ?? 0);
    $jeu = $id ? jeux_par_id($id) : null;
    if (!$jeu) { header('Location: ' . BASE_URL . '/admin?page=exercices'); exit; }

    $id_admin  = $_SESSION['id_admin'];
    afficher_vue('admin/dashboard', [
        'page'                 => 'modifier_exercice',
        'exercices'            => lister_jeux_admin($id_admin),
        'tests'                => lister_test_admin($id_admin),
        'exercice_selectionne' => null,
        'test_selectionne'     => null,
        'jeu'                  => $jeu,
        'types'                => jeux_recuperer_types(),
        'difficultes'          => jeux_recuperer_difficultes(),
    ]);
}

function traiter_modification_jeux()
{
    verifier_admin();
    verifier_csrf();

    require_once ROOT . '/app/models/jeux.php';

    $id           = (int)($_POST['id_jeux']      ?? 0);
    $titre        = trim($_POST['titre']          ?? '');
    $type         = $_POST['categorie']           ?? 'autre';
    $difficulte   = $_POST['difficulte']          ?? 'moyen';
    $bareme       = max(1, intval($_POST['bareme'] ?? 1));
    $statut       = $_POST['statut']              ?? 'brouillon';
    $contenu_html = trim($_POST['contenu_html']   ?? '');

    if (!$id || $titre === '' || $contenu_html === '') {
        header('Location: ' . BASE_URL . '/admin/jeux/modifier?id=' . $id . '&error=champs_manquants');
        exit;
    }

    jeux_mettre_a_jour($id, $titre, $type, $difficulte, $bareme, $statut, $contenu_html);
    header('Location: ' . BASE_URL . '/admin?page=exercices&success=modifie');
    exit;
}

function traiter_suppression_jeux()
{
    verifier_admin();
    verifier_csrf();

    require_once ROOT . '/app/models/jeux.php';

    $id = (int)($_POST['id_jeux'] ?? 0);
    if ($id) jeux_supprimer($id);
    header('Location: ' . BASE_URL . '/admin?page=exercices&success=supprime');
    exit;
}
