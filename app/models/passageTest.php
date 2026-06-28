<?php

function passage_test_par_id($id_passage_test)
{
    global $pdo;

    $sql = "SELECT * FROM passage_test WHERE id_passage_test = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_passage_test]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function creer_passage_test($id_candidat, $id_session) {
    global $pdo;

    $sql = "INSERT INTO passage_test (date_debut, statut, id_candidat, id_session)
            VALUES (NOW(), 'en_cours', ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_candidat, $id_session]);

    return $pdo->lastInsertId();
}

function passage_test_en_cours($id_candidat) {
    global $pdo;

    $sql = "SELECT * FROM passage_test 
            WHERE id_candidat = ? AND statut = 'en_cours'
            ORDER BY id_passage_test DESC LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_candidat]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function terminer_passage_test($id_passage_test, $score) {
    global $pdo;

    $sql = "UPDATE passage_test
            SET date_fin = NOW(),
                statut = 'termine',
                score_total = ?
            WHERE id_passage_test = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$score, $id_passage_test]);
}

function passage_test_expire($id_passage_test)
{
    global $pdo;

    $sql = "UPDATE passage_test
            SET statut = 'expire',
                date_fin = NOW(),
                score_total = 0
            WHERE id_passage_test = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_passage_test]);
}
