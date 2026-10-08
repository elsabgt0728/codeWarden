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

// Exercices actifs partagés au sein d'UN établissement (ceux de tous les
// admins de cet établissement, pas de la plateforme entière).
// Avant : retournait les exercices actifs de TOUS les admins, toutes écoles confondues.
function jeux_tous_actifs($id_etablissement)
{
    $db   = connecter_bdd();
    $stmt = $db->prepare("
        SELECT j.* FROM JEUX j
        JOIN ADMINISTRATEUR a ON a.id_admin = j.id_admin
        WHERE j.statut = 'actif' AND a.id_etablissement = ?
    ");
    $stmt->execute([$id_etablissement]);
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

/**
 * Sécurité : ne garde, parmi les IDs de jeux reçus en POST, que ceux qui
 * appartiennent réellement à un admin du même établissement. Protège contre
 * un admin qui bidouillerait la requête pour assigner à un test un exercice
 * privé d'une autre école.
 */
function jeux_filtrer_par_etablissement(array $ids_jeux, $id_etablissement): array
{
    $ids_jeux = array_values(array_unique(array_map('intval', $ids_jeux)));
    if (empty($ids_jeux)) return [];

    $db           = connecter_bdd();
    $placeholders = implode(',', array_fill(0, count($ids_jeux), '?'));
    $params       = array_merge($ids_jeux, [$id_etablissement]);

    $stmt = $db->prepare("
        SELECT j.id_jeux FROM JEUX j
        JOIN ADMINISTRATEUR a ON a.id_admin = j.id_admin
        WHERE j.id_jeux IN ($placeholders) AND a.id_etablissement = ?
    ");
    $stmt->execute($params);

    return array_map('intval', array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id_jeux'));
}
