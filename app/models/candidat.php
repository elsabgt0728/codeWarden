<?php
// Opérations sur la table 'candidat'

function trouver_candidat_par_email($email)
{
    $db  = connecter_bdd();
    $req = $db->prepare('SELECT * FROM candidat WHERE email = ?');
    $req->execute([$email]);
    return $req->fetch();
}

function trouver_candidat_par_id($id)
{
    $db  = connecter_bdd();
    $req = $db->prepare('SELECT * FROM candidat WHERE id_candidat = ?');
    $req->execute([$id]);
    return $req->fetch();
}

// Vérifie si un e-mail est pris par un AUTRE candidat (pour éviter les doublons)
function email_deja_pris($email, $idExclu)
{
    $db  = connecter_bdd();
    $req = $db->prepare('SELECT id_candidat FROM candidat WHERE email = ? AND id_candidat != ?');
    $req->execute([$email, $idExclu]);
    return $req->fetch() !== false;
}

function creer_candidat($nom, $prenom, $email, $motDePasseHache)
{
    $db  = connecter_bdd();
    $req = $db->prepare('INSERT INTO candidat (nom, prenom, email, password) VALUES (?, ?, ?, ?)');
    $req->execute([$nom, $prenom, $email, $motDePasseHache]);
}

function modifier_candidat($id, $nom, $prenom, $email)
{
    $db  = connecter_bdd();
    $req = $db->prepare('UPDATE candidat SET nom = ?, prenom = ?, email = ? WHERE id_candidat = ?');
    $req->execute([$nom, $prenom, $email, $id]);
}
