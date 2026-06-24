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
