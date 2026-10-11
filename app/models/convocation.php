<?php

function convocation_creer($id_candidat, $id_session, $lien_acces, $date_expiration = null)
{
    $db    = connecter_bdd();
    // Jeton personnel et imprévisible : c'est lui, pas l'URL "?id_test=X",
    // qui détermine désormais qui a le droit d'ouvrir ce test précis.
    $token = bin2hex(random_bytes(32));

    $stmt = $db->prepare("INSERT INTO convocation (lien_acces, token, date_expiration, id_candidat, id_session)
                          VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([$lien_acces, $token, $date_expiration, $id_candidat, $id_session]);
}

/**
 * Retrouve la convocation correspondant à un jeton de lien mail, avec
 * l'id_test associé. Renvoie false si le jeton est inconnu.
 */
function convocation_par_token($token)
{
    if (empty($token)) return false;

    $db   = connecter_bdd();
    $stmt = $db->prepare("
        SELECT conv.*, s.id_test
        FROM convocation conv
        JOIN session s ON s.id_session = conv.id_session
        WHERE conv.token = ?
    ");
    $stmt->execute([$token]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: false;
}

/**
 * Génère un nouveau code à 6 chiffres pour cette convocation, valable 10
 * minutes, et remet le compteur de tentatives à zéro. Renvoie le code en
 * clair (à envoyer par e-mail immédiatement, il n'est jamais renvoyé ensuite).
 */
function convocation_generer_otp($id_convocation)
{
    $db   = connecter_bdd();
    $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    $stmt = $db->prepare("
        UPDATE convocation
        SET otp_code = ?, otp_expiration = DATE_ADD(NOW(), INTERVAL 10 MINUTE), otp_tentatives = 0
        WHERE id_convocation = ?
    ");
    $stmt->execute([$code, $id_convocation]);

    return $code;
}

/**
 * Vérifie le code saisi par le candidat pour cette convocation.
 * Renvoie 'ok', ou un code d'erreur : 'incorrect', 'expire', 'trop_tentatives', 'introuvable'.
 */
function convocation_verifier_otp($id_convocation, $code_saisi): string
{
    $db   = connecter_bdd();
    $stmt = $db->prepare("SELECT otp_code, otp_expiration, otp_tentatives FROM convocation WHERE id_convocation = ?");
    $stmt->execute([$id_convocation]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row || $row['otp_code'] === null) return 'introuvable';
    if ((int)$row['otp_tentatives'] >= 5) return 'trop_tentatives';

    // Chaque tentative (correcte ou non) compte, pour limiter le bruteforce d'un code à 6 chiffres
    $db->prepare("UPDATE convocation SET otp_tentatives = otp_tentatives + 1 WHERE id_convocation = ?")
       ->execute([$id_convocation]);

    if (!$row['otp_expiration'] || strtotime($row['otp_expiration']) < time()) return 'expire';
    if (!hash_equals((string)$row['otp_code'], (string)$code_saisi)) return 'incorrect';

    $db->prepare("UPDATE convocation SET otp_verifie = 1 WHERE id_convocation = ?")->execute([$id_convocation]);
    return 'ok';
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
        SELECT c.id_candidat, c.nom, c.prenom, c.email, conv.lien_acces, conv.token
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
