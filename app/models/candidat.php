<?php
// Opérations sur la table 'candidat'

function trouver_candidat_par_email($email)
{
    $db  = connecter_bdd();
    $req = $db->prepare('SELECT * FROM candidat WHERE email = ?');
    $req->execute([$email]);
    return $req->fetch();
}

function trouver_candidat_par_id($id)
{
    $db  = connecter_bdd();
    $req = $db->prepare('SELECT * FROM candidat WHERE id_candidat = ?');
    $req->execute([$id]);
    return $req->fetch();
}

// Vérifie si un e-mail est pris par un AUTRE candidat (pour éviter les doublons)
function email_deja_pris($email, $idExclu)
{
    $db  = connecter_bdd();
    $req = $db->prepare('SELECT id_candidat FROM candidat WHERE email = ? AND id_candidat != ?');
    $req->execute([$email, $idExclu]);
    return $req->fetch() !== false;
}

function creer_candidat($nom, $prenom, $email, $motDePasseHache, $id_etablissement)
{
    $db  = connecter_bdd();
    $req = $db->prepare('INSERT INTO candidat (nom, prenom, email, password, id_etablissement) VALUES (?, ?, ?, ?, ?)');
    $req->execute([$nom, $prenom, $email, $motDePasseHache, $id_etablissement]);
}

/**
 * Renvoie l'id du seul établissement existant (usage mono-tenant : tant que
 * l'app ne gère qu'une seule école, tout nouveau candidat lui est rattaché
 * automatiquement à l'inscription).
 */
function etablissement_id_par_defaut()
{
    $db   = connecter_bdd();
    $stmt = $db->query("SELECT id_etablissement FROM ETABLISSEMENT ORDER BY id_etablissement ASC LIMIT 1");
    $row  = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? (int)$row['id_etablissement'] : null;
}

function modifier_candidat($id, $nom, $prenom, $email)
{
    $db  = connecter_bdd();
    $req = $db->prepare('UPDATE candidat SET nom = ?, prenom = ?, email = ? WHERE id_candidat = ?');
    $req->execute([$nom, $prenom, $email, $id]);
}

// Candidats actifs d'UN établissement (celui de l'admin connecté).
// Avant : retournait tous les candidats actifs de la plateforme, toutes écoles confondues.
function candidats_tous_actifs($id_etablissement)
{
    $db   = connecter_bdd();
    $stmt = $db->prepare("SELECT * FROM CANDIDAT WHERE statut = 'actif' AND id_etablissement = ?");
    $stmt->execute([$id_etablissement]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Sécurité : ne garde, parmi les IDs candidats reçus en POST, que ceux qui
 * appartiennent réellement à l'établissement de l'admin connecté. Protège
 * contre un admin qui bidouillerait la requête pour convoquer un candidat
 * d'une autre école (même si l'UI ne lui en propose normalement pas).
 */
function candidats_filtrer_par_etablissement(array $ids_candidats, $id_etablissement): array
{
    $ids_candidats = array_values(array_unique(array_map('intval', $ids_candidats)));
    if (empty($ids_candidats)) return [];

    $db           = connecter_bdd();
    $placeholders = implode(',', array_fill(0, count($ids_candidats), '?'));
    $params       = array_merge($ids_candidats, [$id_etablissement]);

    $stmt = $db->prepare("SELECT id_candidat FROM candidat WHERE id_candidat IN ($placeholders) AND id_etablissement = ?");
    $stmt->execute($params);

    return array_map('intval', array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id_candidat'));
}
