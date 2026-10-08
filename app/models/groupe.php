<?php
// Opérations sur les groupes de candidats (GROUPE / CANDIDAT_GROUPE)

function lister_groupes_etablissement($id_etablissement)
{
    $db   = connecter_bdd();
    $stmt = $db->prepare("
        SELECT g.*, COUNT(cg.id_candidat) AS nb_candidats
        FROM GROUPE g
        LEFT JOIN CANDIDAT_GROUPE cg ON cg.id_groupe = g.id_groupe
        WHERE g.id_etablissement = ?
        GROUP BY g.id_groupe
        ORDER BY g.annee DESC, g.nom ASC
    ");
    $stmt->execute([$id_etablissement]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function groupe_par_id($id_groupe, $id_etablissement)
{
    $db   = connecter_bdd();
    $stmt = $db->prepare("SELECT * FROM GROUPE WHERE id_groupe = ? AND id_etablissement = ?");
    $stmt->execute([$id_groupe, $id_etablissement]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function groupe_creer($nom, $promotion, $annee, $id_etablissement)
{
    $db   = connecter_bdd();
    $stmt = $db->prepare("INSERT INTO GROUPE (nom, promotion, annee, id_etablissement) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nom, $promotion ?: null, $annee ?: null, $id_etablissement]);
    return $db->lastInsertId();
}

function groupe_supprimer($id_groupe, $id_etablissement)
{
    $db = connecter_bdd();
    // On ne supprime que si le groupe appartient bien à l'établissement de l'admin
    $stmt = $db->prepare("DELETE FROM GROUPE WHERE id_groupe = ? AND id_etablissement = ?");
    return $stmt->execute([$id_groupe, $id_etablissement]);
}

// Candidats membres d'un groupe
function groupe_candidats($id_groupe)
{
    $db   = connecter_bdd();
    $stmt = $db->prepare("
        SELECT c.* FROM CANDIDAT c
        JOIN CANDIDAT_GROUPE cg ON cg.id_candidat = c.id_candidat
        WHERE cg.id_groupe = ?
        ORDER BY c.nom, c.prenom
    ");
    $stmt->execute([$id_groupe]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function groupe_ajouter_candidat($id_groupe, $id_candidat, $id_etablissement)
{
    $db = connecter_bdd();

    // Vérifie que le groupe ET le candidat appartiennent bien à cet établissement
    $stmt = $db->prepare("
        SELECT g.id_groupe
        FROM GROUPE g
        JOIN CANDIDAT c ON c.id_etablissement = g.id_etablissement
        WHERE g.id_groupe = ? AND c.id_candidat = ? AND g.id_etablissement = ?
    ");
    $stmt->execute([$id_groupe, $id_candidat, $id_etablissement]);
    if (!$stmt->fetch()) return false;

    $stmt = $db->prepare("SELECT id_candidat_groupe FROM CANDIDAT_GROUPE WHERE id_candidat = ? AND id_groupe = ?");
    $stmt->execute([$id_candidat, $id_groupe]);
    if ($stmt->fetch()) return true; // déjà membre, rien à faire

    $stmt = $db->prepare("INSERT INTO CANDIDAT_GROUPE (id_candidat, id_groupe) VALUES (?, ?)");
    return $stmt->execute([$id_candidat, $id_groupe]);
}

function groupe_retirer_candidat($id_groupe, $id_candidat)
{
    $db   = connecter_bdd();
    $stmt = $db->prepare("DELETE FROM CANDIDAT_GROUPE WHERE id_groupe = ? AND id_candidat = ?");
    return $stmt->execute([$id_groupe, $id_candidat]);
}

/**
 * Résout une liste d'id_groupe en la liste unique des id_candidat membres.
 * Vérifie que chaque groupe appartient bien à l'établissement donné — même
 * logique de défense en profondeur que candidats_filtrer_par_etablissement().
 */
function candidats_par_groupes(array $ids_groupes, $id_etablissement): array
{
    $ids_groupes = array_values(array_unique(array_map('intval', $ids_groupes)));
    if (empty($ids_groupes)) return [];

    $db           = connecter_bdd();
    $placeholders = implode(',', array_fill(0, count($ids_groupes), '?'));
    $params       = array_merge($ids_groupes, [$id_etablissement]);

    $stmt = $db->prepare("
        SELECT DISTINCT cg.id_candidat
        FROM CANDIDAT_GROUPE cg
        JOIN GROUPE g ON g.id_groupe = cg.id_groupe
        WHERE cg.id_groupe IN ($placeholders) AND g.id_etablissement = ?
    ");
    $stmt->execute($params);

    return array_map('intval', array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id_candidat'));
}
