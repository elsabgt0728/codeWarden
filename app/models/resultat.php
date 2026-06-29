<?php

function resultat_par_passage($id_passage_test)
{
    $db   = connecter_bdd();
    $stmt = $db->prepare("SELECT * FROM resultat WHERE id_passage_test = ?");
    $stmt->execute([$id_passage_test]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function creer_resultat($id_passage_test, $score)
{
    $db   = connecter_bdd();
    $stmt = $db->prepare("INSERT INTO resultat (score_global, id_passage_test) VALUES (?, ?)");
    $stmt->execute([$score, $id_passage_test]);
}

function resultat_mettre_a_jour($id_passage_test, $score)
{
    $db   = connecter_bdd();
    $stmt = $db->prepare("UPDATE resultat SET score_global = ? WHERE id_passage_test = ?");
    $stmt->execute([$score, $id_passage_test]);
}

function resultat_mettre_a_jour_decision($id_passage_test, $decision)
{
    $db   = connecter_bdd();
    $stmt = $db->prepare("UPDATE resultat SET decision = ? WHERE id_passage_test = ?");
    return $stmt->execute([$decision, $id_passage_test]);
}

function enregistrer_score_jeu($id_passage_test, $id_jeux, $score)
{
    $db = connecter_bdd();
    // Supprimer l'éventuel doublon (refresh après avoir joué ce jeu)
    $db->prepare("DELETE FROM reponse WHERE id_passage_test = ? AND id_jeux = ?")
       ->execute([$id_passage_test, $id_jeux]);
    $db->prepare("INSERT INTO reponse (id_passage_test, id_jeux, points_obtenus, est_correcte) VALUES (?, ?, ?, ?)")
       ->execute([$id_passage_test, $id_jeux, $score, $score >= 50 ? 1 : 0]);
}

function reponses_scores_par_passage($id_passage_test)
{
    $db   = connecter_bdd();
    $stmt = $db->prepare("SELECT id_jeux, points_obtenus FROM reponse WHERE id_passage_test = ? ORDER BY id_reponse ASC");
    $stmt->execute([$id_passage_test]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function resultats_candidats_liste()
{
    $db   = connecter_bdd();
    $stmt = $db->query("
        SELECT c.id_candidat, c.nom, c.prenom, c.email,
               t.titre AS titre_test,
               conv.date_expiration,
               pt.id_passage_test, pt.statut AS statut_passage, pt.date_debut, pt.date_fin,
               r.id_resultat, r.score_global, r.decision,
               GROUP_CONCAT(
                   CONCAT(j.titre, ' : ', ROUND(rep.points_obtenus, 0), '%')
                   ORDER BY rep.id_reponse ASC
                   SEPARATOR ' | '
               ) AS scores_par_jeu
        FROM convocation conv
        JOIN session  s ON s.id_session  = conv.id_session
        JOIN test     t ON t.id_test     = s.id_test
        JOIN candidat c ON c.id_candidat = conv.id_candidat
        LEFT JOIN passage_test pt ON pt.id_candidat = c.id_candidat AND pt.id_session = conv.id_session
        LEFT JOIN resultat  r   ON r.id_passage_test   = pt.id_passage_test
        LEFT JOIN reponse   rep ON rep.id_passage_test = pt.id_passage_test
        LEFT JOIN jeux      j   ON j.id_jeux           = rep.id_jeux
        GROUP BY c.id_candidat, conv.id_convocation, pt.id_passage_test
        ORDER BY c.nom, c.prenom
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
