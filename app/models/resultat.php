<?php

// Récupérer un résultat via id_passage_test
function resultat_par_passage($id_passage_test)
{
    global $pdo;

    $sql = "SELECT * FROM resultat WHERE id_passage_test = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_passage_test]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Créer un résultat
function creer_resultat($id_passage_test, $score)
{
    global $pdo;

    $sql = "INSERT INTO resultat (score_global, id_passage_test)
            VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$score, $id_passage_test]);
}

// Mettre à jour un résultat (si recalcul)
function resultat_mettre_a_jour($id_passage_test, $score)
{
    global $pdo;

    $sql = "UPDATE resultat
            SET score_global = ?
            WHERE id_passage_test = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$score, $id_passage_test]);
}
