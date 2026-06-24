<?php

function session_creer($id_test, $id_admin, $date_debut, $date_fin = null)
{
    $db = connecter_bdd();

    $sql = "
        INSERT INTO SESSION (date_debut, date_fin, statut, id_test, id_admin)
        VALUES (?, ?, 'planifiee', ?, ?)
    ";

    $stmt = $db->prepare($sql);
    $stmt->execute([$date_debut, $date_fin, $id_test, $id_admin]);

    return $db->lastInsertId();
}
