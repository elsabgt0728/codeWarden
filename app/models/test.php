<?php

function tests_creer($titre, $duree_minutes, $statut, $id_admin)
{
    $db = connecter_bdd();

    $sql = "
        INSERT INTO TEST (titre, duree_minutes, statut, id_admin)
        VALUES (?, ?, ?, ?)
    ";

    $stmt = $db->prepare($sql);
    $stmt->execute([$titre, $duree_minutes, $statut, $id_admin]);

    return $db->lastInsertId();
}

function tests_ajouter_jeu($id_test, $id_jeux, $position, $points_max = 1)
{
    $db = connecter_bdd();

    $sql = "
        INSERT INTO TEST_JEUX (id_test, id_jeux, position, points_max)
        VALUES (?, ?, ?, ?)
    ";

    $stmt = $db->prepare($sql);
    return $stmt->execute([$id_test, $id_jeux, $position, $points_max]);
}




function test_recuperer_par_id($id_test) {
    $db = connecter_bdd();
    $stmt = $db->prepare("SELECT * FROM TEST WHERE id_test = ?");
    $stmt->execute([$id_test]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function test_recuperer_jeux($id_test) {
    $db = connecter_bdd();
    $stmt = $db->prepare("
        SELECT J.*
        FROM TEST_JEUX TJ
        JOIN JEUX J ON J.id_jeux = TJ.id_jeux
        WHERE TJ.id_test = ?
        ORDER BY TJ.position ASC
    ");
    $stmt->execute([$id_test]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
