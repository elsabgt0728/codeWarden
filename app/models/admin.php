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
