<?php

function jeux_inserer($titre, $type, $difficulte, $bareme, $contenu_json, $statut, $id_admin)
{
    $db = connecter_bdd();

    $sql = "
        INSERT INTO JEUX (titre, type, difficulte, bareme, contenu_json, statut, id_admin)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ";

    $stmt = $db->prepare($sql);

    return $stmt->execute([
        $titre,
        $type,
        $difficulte,
        $bareme,
        $contenu_json,
        $statut,
        $id_admin
    ]);
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
