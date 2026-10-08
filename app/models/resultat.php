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

    // Garde-fou : le score de chaque jeu est auto-déclaré par le JS de
    // l'iframe (architecture actuelle des exercices HTML), donc impossible à
    // vérifier entièrement côté serveur sans revoir le format des exercices.
    // On borne au moins à [0, 100] pour éviter qu'une valeur aberrante
    // (négative, >100, modifiée via les devtools) ne pollue la base.
    $score = max(0, min(100, (float)$score));

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

/**
 * Recalcule le score total d'un passage à partir des scores PAR JEU déjà
 * enregistrés en base (table reponse, remplie au fur et à mesure via
 * /candidat/score-jeu), pondérés par le barème de chaque jeu.
 *
 * Remplace le score final envoyé par le client à la fin du test, qui pouvait
 * être falsifié par un simple appel direct à /candidat/terminer avec un
 * score arbitraire — alors qu'au moment où ce endpoint est appelé, tous les
 * scores par jeu sont déjà enregistrés côté serveur.
 */
function calculer_score_total_passage($id_passage_test, $id_test)
{
    require_once ROOT . '/app/models/test.php';

    $jeux_test = test_recuperer_jeux($id_test);
    $baremes   = [];
    foreach ($jeux_test as $j) {
        $baremes[(int)$j['id_jeux']] = max(1, (int)$j['bareme']);
    }

    $scores = reponses_scores_par_passage($id_passage_test);

    $total_points = 0.0;
    $total_bareme = 0;
    foreach ($scores as $s) {
        $id_jeux = (int)$s['id_jeux'];
        $bareme  = $baremes[$id_jeux] ?? 1; // jeu retiré du test entre-temps : barème par défaut
        $total_points += ((float)$s['points_obtenus'] / 100) * $bareme;
        $total_bareme += $bareme;
    }

    if ($total_bareme <= 0) return 0.0;

    return round(($total_points / $total_bareme) * 100, 2);
}

function resultats_candidats_liste($id_etablissement)
{
    $db   = connecter_bdd();
    $stmt = $db->prepare("
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
        WHERE c.id_etablissement = ?
        GROUP BY c.id_candidat, conv.id_convocation, pt.id_passage_test
        ORDER BY c.nom, c.prenom
    ");
    $stmt->execute([$id_etablissement]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
