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
    $req = $db->prepare('SELECT id_jeux, titre, type, difficulte, bareme, statut FROM jeux WHERE id_admin = ? ORDER BY id_jeux ASC');
    $req->execute([$id_admin]);
    return $req->fetchAll();
}

function lister_test_admin($id_admin)
{
    $db  = connecter_bdd();
    $req = $db->prepare("
        SELECT t.id_test, t.titre, t.statut, t.date_creation, t.duree_minutes,
               COUNT(DISTINCT conv.id_convocation) AS nb_convoques,
               MAX(CASE WHEN conv.date_expiration IS NOT NULL THEN 1 ELSE 0 END) AS est_convoque
        FROM test t
        LEFT JOIN session      s    ON s.id_test      = t.id_test
        LEFT JOIN convocation  conv ON conv.id_session = s.id_session
        WHERE t.id_admin = ?
        GROUP BY t.id_test
        ORDER BY t.id_test ASC
    ");
    $req->execute([$id_admin]);
    return $req->fetchAll();
}