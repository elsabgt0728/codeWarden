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

function test_mettre_a_jour($id, $titre, $duree, $statut)
{
    $db = connecter_bdd();
    $stmt = $db->prepare("UPDATE test SET titre=?, duree_minutes=?, statut=? WHERE id_test=?");
    return $stmt->execute([$titre, $duree, $statut, $id]);
}

function test_supprimer($id)
{
    $db = connecter_bdd();
    // SESSION référence TEST avec RESTRICT — vider les dépendances d'abord
    $sessions = $db->prepare("SELECT id_session FROM session WHERE id_test = ?");
    $sessions->execute([$id]);
    foreach ($sessions->fetchAll(PDO::FETCH_ASSOC) as $s) {
        $sid = $s['id_session'];
        // PASSAGE_TEST référence SESSION avec RESTRICT — supprimer (REPONSE et RESULTAT cascadent)
        $passages = $db->prepare("SELECT id_passage_test FROM passage_test WHERE id_session = ?");
        $passages->execute([$sid]);
        foreach ($passages->fetchAll(PDO::FETCH_ASSOC) as $p) {
            $db->prepare("DELETE FROM passage_test WHERE id_passage_test = ?")->execute([$p['id_passage_test']]);
        }
        // SESSION (CONVOCATION cascade)
        $db->prepare("DELETE FROM session WHERE id_session = ?")->execute([$sid]);
    }
    // TEST (TEST_JEUX cascade)
    $db->prepare("DELETE FROM test WHERE id_test = ?")->execute([$id]);
}

function test_mettre_a_jour_jeux($id_test, array $jeux_ids)
{
    $db = connecter_bdd();
    $db->prepare("DELETE FROM test_jeux WHERE id_test = ?")->execute([$id_test]);
    $pos = 1;
    foreach ($jeux_ids as $id_jeux) {
        $db->prepare("INSERT INTO test_jeux (id_test, id_jeux, position, points_max) VALUES (?, ?, ?, 1)")
           ->execute([$id_test, (int)$id_jeux, $pos++]);
    }
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
