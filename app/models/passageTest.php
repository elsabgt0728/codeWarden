<?php

function passage_test_par_id($id_passage_test)
{
    $db   = connecter_bdd();
    $stmt = $db->prepare("SELECT * FROM passage_test WHERE id_passage_test = ?");
    $stmt->execute([$id_passage_test]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function creer_passage_test($id_candidat, $id_session)
{
    $db   = connecter_bdd();
    $stmt = $db->prepare("INSERT INTO passage_test (date_debut, statut, id_candidat, id_session)
                          VALUES (NOW(), 'en_cours', ?, ?)");
    $stmt->execute([$id_candidat, $id_session]);
    return $db->lastInsertId();
}

function passage_test_en_cours($id_candidat)
{
    $db   = connecter_bdd();
    $stmt = $db->prepare("SELECT * FROM passage_test
                          WHERE id_candidat = ? AND statut = 'en_cours'
                          ORDER BY id_passage_test DESC LIMIT 1");
    $stmt->execute([$id_candidat]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function terminer_passage_test($id_passage_test, $score)
{
    $db   = connecter_bdd();
    $stmt = $db->prepare("UPDATE passage_test
                          SET date_fin = NOW(), statut = 'termine', score_total = ?
                          WHERE id_passage_test = ?");
    $stmt->execute([$score, $id_passage_test]);
}

function passage_test_termine_avec_resultat($id_candidat)
{
    $db   = connecter_bdd();
    $stmt = $db->prepare("
        SELECT pt.*, r.score_global, r.decision, r.id_resultat,
               t.titre AS titre_test, t.duree_minutes
        FROM passage_test pt
        JOIN session s      ON s.id_session      = pt.id_session
        JOIN test t         ON t.id_test         = s.id_test
        LEFT JOIN resultat r ON r.id_passage_test = pt.id_passage_test
        WHERE pt.id_candidat = ? AND pt.statut IN ('termine', 'expire')
        ORDER BY pt.date_fin DESC
        LIMIT 1
    ");
    $stmt->execute([$id_candidat]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function candidat_a_deja_passe($id_candidat, $id_test)
{
    $db   = connecter_bdd();
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM passage_test pt
        JOIN session s ON s.id_session = pt.id_session
        WHERE pt.id_candidat = ?
          AND s.id_test = ?
          AND pt.statut IN ('termine', 'expire')
    ");
    $stmt->execute([$id_candidat, $id_test]);
    return (int)$stmt->fetchColumn() > 0;
}

function passage_candidat_et_test($id_passage_test)
{
    $db   = connecter_bdd();
    $stmt = $db->prepare("
        SELECT c.nom, c.prenom, c.email, t.titre AS titre_test
        FROM passage_test pt
        JOIN session  s ON s.id_session  = pt.id_session
        JOIN test     t ON t.id_test     = s.id_test
        JOIN candidat c ON c.id_candidat = pt.id_candidat
        WHERE pt.id_passage_test = ?
    ");
    $stmt->execute([$id_passage_test]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function passage_test_expire($id_passage_test)
{
    $db   = connecter_bdd();
    $stmt = $db->prepare("UPDATE passage_test
                          SET statut = 'expire', date_fin = NOW(), score_total = 0
                          WHERE id_passage_test = ?");
    $stmt->execute([$id_passage_test]);
}

/**
 * Sécurité : vérifie qu'un id_passage_test envoyé par le client (JS ou URL)
 * appartient bien au candidat actuellement connecté, avant toute lecture ou
 * écriture dessus. Sans ça, un onglet resté ouvert sur le passage d'un AUTRE
 * candidat (ou un id_passage deviné/modifié dans la requête) peut faire
 * enregistrer un score ou afficher un résultat sur le passage de quelqu'un
 * d'autre.
 */
function passage_appartient_candidat($id_passage_test, $id_candidat): bool
{
    $db   = connecter_bdd();
    $stmt = $db->prepare("SELECT COUNT(*) FROM passage_test WHERE id_passage_test = ? AND id_candidat = ?");
    $stmt->execute([$id_passage_test, $id_candidat]);
    return (int)$stmt->fetchColumn() > 0;
}
