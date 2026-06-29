<?php

function _stats_filtres_passage(array $f): array
{
    $where  = [];
    $params = [];

    if (!empty($f['date_debut']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $f['date_debut'])) {
        $where[]  = 'pt.date_debut >= ?';
        $params[] = $f['date_debut'] . ' 00:00:00';
    }
    if (!empty($f['date_fin']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $f['date_fin'])) {
        $where[]  = 'pt.date_debut <= ?';
        $params[] = $f['date_fin'] . ' 23:59:59';
    }
    $statuts_valides = ['en_cours', 'termine', 'abandonne', 'expire'];
    if (!empty($f['statut']) && in_array($f['statut'], $statuts_valides, true)) {
        $where[]  = 'pt.statut = ?';
        $params[] = $f['statut'];
    }

    return [$where, $params];
}

function stats_candidat(int $id_candidat, array $filtres = []): array
{
    $db = connecter_bdd();

    [$where, $params] = _stats_filtres_passage($filtres);

    if (!empty($filtres['id_test']) && is_numeric($filtres['id_test'])) {
        $where[]  = 's.id_test = ?';
        $params[] = (int) $filtres['id_test'];
    }

    $cond      = $where ? ('AND ' . implode(' AND ', $where)) : '';
    $allParams = array_merge([$id_candidat], $params);

    $sql = "
        SELECT
            pt.id_passage_test,
            pt.date_debut,
            pt.date_fin,
            pt.statut,
            pt.score_total,
            r.score_global,
            r.decision,
            t.titre  AS titre_test,
            t.id_test,
            TIMESTAMPDIFF(SECOND, pt.date_debut, pt.date_fin)        AS duree_secondes,
            (SELECT COUNT(*) FROM reponse rp
             WHERE rp.id_passage_test = pt.id_passage_test
               AND rp.est_correcte = 1)                              AS nb_bonnes_reponses,
            (SELECT COUNT(*) FROM reponse rp
             WHERE rp.id_passage_test = pt.id_passage_test)          AS nb_questions,
            (SELECT ROUND(AVG(rp.temps_reponse_sec), 1) FROM reponse rp
             WHERE rp.id_passage_test = pt.id_passage_test)          AS temps_moyen_reponse_sec
        FROM passage_test pt
        LEFT JOIN session   s ON s.id_session      = pt.id_session
        LEFT JOIN test      t ON t.id_test         = s.id_test
        LEFT JOIN resultat  r ON r.id_passage_test = pt.id_passage_test
        WHERE pt.id_candidat = ?
        $cond
        ORDER BY pt.date_debut DESC
    ";

    $stmt = $db->prepare($sql);
    $stmt->execute($allParams);
    $passages = $stmt->fetchAll();

    $scores  = array_filter(array_column($passages, 'score_global'), fn($v) => $v !== null);
    $resume  = [
        'nb_passages'    => count($passages),
        'score_moyen'    => $scores ? round(array_sum($scores) / count($scores), 1) : null,
        'meilleur_score' => $scores ? round((float) max($scores), 1) : null,
    ];

    return compact('passages', 'resume');
}

function stats_admin_global(array $filtres = []): array
{
    $db = connecter_bdd();

    [$where, $params] = _stats_filtres_passage($filtres);

    if (!empty($filtres['id_test']) && is_numeric($filtres['id_test'])) {
        $where[]  = 's.id_test = ?';
        $params[] = (int) $filtres['id_test'];
    }

    $cond = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

    $sql = "
        SELECT
            COUNT(DISTINCT pt.id_candidat)                                AS nb_candidats,
            COUNT(pt.id_passage_test)                                     AS nb_passages,
            ROUND(AVG(r.score_global), 1)                                 AS score_moyen,
            COUNT(r.id_resultat)                                          AS nb_avec_resultat,
            SUM(CASE WHEN r.decision = 'admis'         THEN 1 ELSE 0 END) AS nb_admis,
            SUM(CASE WHEN r.decision = 'refuse'        THEN 1 ELSE 0 END) AS nb_refuses,
            SUM(CASE WHEN r.decision = 'liste_attente' THEN 1 ELSE 0 END) AS nb_liste_attente,
            SUM(CASE WHEN r.decision = 'en_attente'    THEN 1 ELSE 0 END) AS nb_en_attente,
            ROUND(
                SUM(CASE WHEN r.decision = 'admis' THEN 1 ELSE 0 END)
                / NULLIF(COUNT(r.id_resultat), 0) * 100, 1
            )                                                             AS taux_reussite
        FROM passage_test pt
        LEFT JOIN session  s ON s.id_session      = pt.id_session
        LEFT JOIN resultat r ON r.id_passage_test = pt.id_passage_test
        $cond
    ";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch() ?: [];
}

function stats_admin_epreuve(int $id_test, array $filtres = []): array
{
    $db = connecter_bdd();

    [$where, $params] = _stats_filtres_passage($filtres);
    $where[]  = 's.id_test = ?';
    $params[] = $id_test;

    $cond = 'WHERE ' . implode(' AND ', $where);

    $sql = "
        SELECT
            t.id_test,
            t.titre,
            COUNT(DISTINCT pt.id_candidat)                                AS nb_candidats,
            COUNT(pt.id_passage_test)                                     AS nb_passages,
            ROUND(AVG(r.score_global), 1)                                 AS score_moyen,
            ROUND(MIN(r.score_global), 1)                                 AS score_min,
            ROUND(MAX(r.score_global), 1)                                 AS score_max,
            ROUND(AVG(TIMESTAMPDIFF(SECOND, pt.date_debut, pt.date_fin)), 0) AS duree_moyenne_secondes,
            COUNT(r.id_resultat)                                          AS nb_avec_resultat,
            SUM(CASE WHEN r.decision = 'admis'         THEN 1 ELSE 0 END) AS nb_admis,
            SUM(CASE WHEN r.decision = 'refuse'        THEN 1 ELSE 0 END) AS nb_refuses,
            SUM(CASE WHEN r.decision = 'liste_attente' THEN 1 ELSE 0 END) AS nb_liste_attente,
            SUM(CASE WHEN r.decision = 'en_attente'    THEN 1 ELSE 0 END) AS nb_en_attente,
            ROUND(
                SUM(CASE WHEN r.decision = 'admis' THEN 1 ELSE 0 END)
                / NULLIF(COUNT(r.id_resultat), 0) * 100, 1
            )                                                             AS taux_reussite
        FROM passage_test pt
        JOIN   session  s ON s.id_session      = pt.id_session
        JOIN   test     t ON t.id_test         = s.id_test
        LEFT JOIN resultat r ON r.id_passage_test = pt.id_passage_test
        $cond
    ";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $resume = $stmt->fetch() ?: [];

    $sqlClass = "
        SELECT
            r.rang,
            c.prenom,
            c.nom,
            c.email,
            ROUND(r.score_global, 1) AS score_global,
            r.decision,
            pt.id_passage_test
        FROM passage_test pt
        JOIN session   s ON s.id_session      = pt.id_session
        JOIN candidat  c ON c.id_candidat     = pt.id_candidat
        JOIN resultat  r ON r.id_passage_test = pt.id_passage_test
        WHERE s.id_test = ?
        ORDER BY r.score_global DESC
        LIMIT 50
    ";
    $stmtC = $db->prepare($sqlClass);
    $stmtC->execute([$id_test]);
    $classement = $stmtC->fetchAll();

    return compact('resume', 'classement');
}

function stats_admin_comparaison(array $filtres = []): array
{
    $db = connecter_bdd();

    [$where, $params] = _stats_filtres_passage($filtres);
    $cond = $where ? ('AND ' . implode(' AND ', $where)) : '';

    $sql = "
        SELECT
            t.id_test,
            t.titre,
            COUNT(DISTINCT pt.id_candidat)                                AS nb_candidats,
            COUNT(pt.id_passage_test)                                     AS nb_passages,
            ROUND(AVG(r.score_global), 1)                                 AS score_moyen,
            ROUND(MIN(r.score_global), 1)                                 AS score_min,
            ROUND(MAX(r.score_global), 1)                                 AS score_max,
            ROUND(
                SUM(CASE WHEN r.decision = 'admis' THEN 1 ELSE 0 END)
                / NULLIF(COUNT(r.id_resultat), 0) * 100, 1
            )                                                             AS taux_reussite
        FROM test t
        LEFT JOIN session      s  ON s.id_test          = t.id_test
        LEFT JOIN passage_test pt ON pt.id_session      = s.id_session
        LEFT JOIN resultat     r  ON r.id_passage_test  = pt.id_passage_test
        WHERE 1 = 1 $cond
        GROUP BY t.id_test, t.titre
        ORDER BY t.id_test ASC
    ";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}
