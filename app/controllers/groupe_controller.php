<?php

function traiter_creation_groupe()
{
    verifier_admin();
    verifier_csrf();
    require_once ROOT . '/app/models/groupe.php';

    $id_etablissement = $_SESSION['id_etablissement'];
    $nom       = trim($_POST['nom']       ?? '');
    $promotion = trim($_POST['promotion'] ?? '');
    $annee     = $_POST['annee'] ?? null;

    if ($nom === '') {
        header('Location: ' . BASE_URL . '/admin?page=groupes&error=nom_manquant');
        exit;
    }

    $id_groupe = groupe_creer($nom, $promotion, $annee ?: null, $id_etablissement);
    header('Location: ' . BASE_URL . '/admin?page=groupes&groupe_id=' . $id_groupe . '&success=cree');
    exit;
}

function traiter_suppression_groupe()
{
    verifier_admin();
    verifier_csrf();
    require_once ROOT . '/app/models/groupe.php';

    $id_etablissement = $_SESSION['id_etablissement'];
    $id_groupe = (int)($_POST['id_groupe'] ?? 0);
    if ($id_groupe) groupe_supprimer($id_groupe, $id_etablissement);

    header('Location: ' . BASE_URL . '/admin?page=groupes&success=supprime');
    exit;
}

function traiter_ajout_candidat_groupe()
{
    verifier_admin();
    verifier_csrf();
    require_once ROOT . '/app/models/groupe.php';

    $id_etablissement = $_SESSION['id_etablissement'];
    $id_groupe   = (int)($_POST['id_groupe']   ?? 0);
    $id_candidat = (int)($_POST['id_candidat'] ?? 0);

    if ($id_groupe && $id_candidat) {
        groupe_ajouter_candidat($id_groupe, $id_candidat, $id_etablissement);
    }

    header('Location: ' . BASE_URL . '/admin?page=groupes&groupe_id=' . $id_groupe);
    exit;
}

function traiter_retrait_candidat_groupe()
{
    verifier_admin();
    verifier_csrf();
    require_once ROOT . '/app/models/groupe.php';

    $id_groupe   = (int)($_POST['id_groupe']   ?? 0);
    $id_candidat = (int)($_POST['id_candidat'] ?? 0);

    if ($id_groupe && $id_candidat) {
        groupe_retirer_candidat($id_groupe, $id_candidat);
    }

    header('Location: ' . BASE_URL . '/admin?page=groupes&groupe_id=' . $id_groupe);
    exit;
}
