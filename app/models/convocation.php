<?php

function convocation_creer($id_candidat, $id_session, $lien_acces, $date_expiration = null)
{
    $db   = connecter_bdd();
    $stmt = $db->prepare("INSERT INTO convocation (lien_acces, date_expiration, id_candidat, id_session)
                          VALUES (?, ?, ?, ?)");
    return $stmt->execute([$lien_acces, $date_expiration, $id_candidat, $id_session]);
}

function candidat_est_convoque($id_candidat, $id_test)
{
    $db   = connecter_bdd();
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM convocation conv
        JOIN session s ON s.id_session = conv.id_session
        WHERE conv.id_candidat = ?
          AND s.id_test = ?
          AND (conv.date_expiration IS NULL OR conv.date_expiration > NOW())
    ");
    $stmt->execute([$id_candidat, $id_test]);
    return (int)$stmt->fetchColumn() > 0;
}

function test_est_convoque($id_test)
{
    $db   = connecter_bdd();
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM convocation conv
        JOIN session s ON s.id_session = conv.id_session
        WHERE s.id_test = ? AND conv.date_expiration IS NOT NULL
    ");
    $stmt->execute([$id_test]);
    return (int)$stmt->fetchColumn() > 0;
}

function convocations_fixer_expiration($id_test, $jours = 7)
{
    $db   = connecter_bdd();
    $stmt = $db->prepare("
        UPDATE convocation conv
        JOIN session s ON s.id_session = conv.id_session
        SET conv.date_expiration = DATE_ADD(NOW(), INTERVAL ? DAY)
        WHERE s.id_test = ?
    ");
    $stmt->execute([$jours, $id_test]);
}

function convocations_par_test($id_test)
{
    $db   = connecter_bdd();
    $stmt = $db->prepare("
        SELECT c.id_candidat, c.nom, c.prenom, c.email, conv.lien_acces
        FROM convocation conv
        JOIN session   s ON s.id_session  = conv.id_session
        JOIN candidat  c ON c.id_candidat = conv.id_candidat
        WHERE s.id_test = ?
    ");
    $stmt->execute([$id_test]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function convocation_candidat($id_candidat)
{
    $db   = connecter_bdd();
    $stmt = $db->prepare("
        SELECT conv.*, s.id_test, t.titre, t.duree_minutes
        FROM convocation conv
        JOIN session s ON s.id_session = conv.id_session
        JOIN test    t ON t.id_test    = s.id_test
        WHERE conv.id_candidat = ?
          AND s.statut NOT IN ('terminee', 'annulee')
        ORDER BY conv.id_convocation DESC
        LIMIT 1
    ");
    $stmt->execute([$id_candidat]);
    return $stmt->fetch();
}
