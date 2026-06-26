<?php
// Opérations sur la table 'administrateur'

function trouver_admin_par_email($email)
{
    $db  = connecter_bdd();
    $req = $db->prepare('SELECT * FROM administrateur WHERE email = ?');
    $req->execute([$email]);
    return $req->fetch();
}

function trouver_admin_par_id($id)
{
    $db  = connecter_bdd();
    $req = $db->prepare('SELECT * FROM administrateur WHERE id_admin = ?');
    $req->execute([$id]);
    return $req->fetch();
}

function lister_jeux_admin($id_admin)
{
    $db  = connecter_bdd();
    $req = $db->prepare('SELECT id_jeux, titre, type, difficulte, statut FROM jeux WHERE id_admin = ? ORDER BY id_jeux ASC');
    $req->execute([$id_admin]);
    return $req->fetchAll();
}

function lister_test_admin($id_admin)
{
    $db  = connecter_bdd();
    $req = $db->prepare('SELECT id_test, titre, statut, date_creation FROM test WHERE id_admin = ? ORDER BY id_test ASC');
    $req->execute([$id_admin]);
    return $req->fetchAll();
}