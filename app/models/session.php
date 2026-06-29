<?php

function session_creer($id_test, $id_admin, $date_debut, $date_fin = null)
{
    $db   = connecter_bdd();
    $stmt = $db->prepare("INSERT INTO session (date_debut, date_fin, statut, id_test, id_admin)
                          VALUES (?, ?, 'planifiee', ?, ?)");
    $stmt->execute([$date_debut, $date_fin, $id_test, $id_admin]);
    return $db->lastInsertId();
}

function session_recuperer_par_test($id_test)
{
    $db   = connecter_bdd();
    $stmt = $db->prepare("SELECT * FROM session
                          WHERE id_test = ?
                            AND statut NOT IN ('terminee', 'annulee')
                          ORDER BY id_session DESC
                          LIMIT 1");
    $stmt->execute([$id_test]);
    return $stmt->fetch();
}
