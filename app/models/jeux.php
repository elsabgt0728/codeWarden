<?php

function jeux_inserer_html($titre, $type, $difficulte, $bareme, $statut, $contenu_html, $id_admin)
{
    $db  = connecter_bdd();
    $req = $db->prepare('INSERT INTO jeux (titre, type, difficulte, bareme, contenu_html, statut, id_admin)
                         VALUES (?, ?, ?, ?, ?, ?, ?)');
    return $req->execute([$titre, $type, $difficulte, $bareme, $contenu_html, $statut, $id_admin]);
}

function jeux_inserer($titre, $type, $difficulte, $bareme, $contenu_json, $statut, $id_admin)
{
    $db   = connecter_bdd();
    $stmt = $db->prepare('INSERT INTO jeux (titre, type, difficulte, bareme, contenu_json, statut, id_admin)
                          VALUES (?, ?, ?, ?, ?, ?, ?)');
    return $stmt->execute([$titre, $type, $difficulte, $bareme, $contenu_json, $statut, $id_admin]);
}


function jeux_recuperer_types()
{
    $db = connecter_bdd();
    $query = $db->query("SHOW COLUMNS FROM JEUX LIKE 'type'");
    $row = $query->fetch();

    preg_match("/^enum\((.*)\)$/", $row['Type'], $matches);
    return str_getcsv($matches[1], ',', "'");
}

function jeux_recuperer_difficultes()
{
    $db = connecter_bdd();
    $query = $db->query("SHOW COLUMNS FROM JEUX LIKE 'difficulte'");
    $row = $query->fetch();

    preg_match("/^enum\((.*)\)$/", $row['Type'], $matches);
    return str_getcsv($matches[1], ',', "'");
}


function jeux_tous_actifs()
{
    $db = connecter_bdd();
    $stmt = $db->query("SELECT * FROM JEUX WHERE statut = 'actif'");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function jeux_par_id($id)
{
    $db = connecter_bdd();
    $stmt = $db->prepare("SELECT * FROM jeux WHERE id_jeux = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function jeux_mettre_a_jour($id, $titre, $type, $difficulte, $bareme, $statut, $contenu_html)
{
    $db = connecter_bdd();
    $stmt = $db->prepare("UPDATE jeux SET titre=?, type=?, difficulte=?, bareme=?, statut=?, contenu_html=? WHERE id_jeux=?");
    return $stmt->execute([$titre, $type, $difficulte, $bareme, $statut, $contenu_html, $id]);
}

function jeux_supprimer($id)
{
    $db = connecter_bdd();
    // REPONSE référence JEUX avec RESTRICT — supprimer d'abord
    $db->prepare("DELETE FROM reponse WHERE id_jeux = ?")->execute([$id]);
    // TEST_JEUX cascade depuis JEUX
    $db->prepare("DELETE FROM jeux WHERE id_jeux = ?")->execute([$id]);
}
