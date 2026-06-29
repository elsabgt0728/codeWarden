<?php
// Opérations sur la table 'administrateur'

function trouver_admin_par_email($email)
{
    $db  = connecter_bdd();
    $req = $db->prepare('SELECT * FROM administrateur WHERE email = ?');
    $req->execute([$email]);
    return $req->fetch();
}

function trouver_admin_par_id($id)
{
    $db  = connecter_bdd();
    $req = $db->prepare('SELECT * FROM administrateur WHERE id_admin = ?');
    $req->execute([$id]);
    return $req->fetch();
}

// ── JEUX ──────────────────────────────────────────────────────────────────────

function lister_jeux()
{
    $db = connecter_bdd();
    return $db->query('SELECT * FROM JEUX ORDER BY titre')->fetchAll();
}

function creer_jeu($titre, $type, $difficulte, $bareme, $statut, $contenu_json, $id_admin)
{
    $db  = connecter_bdd();
    $req = $db->prepare(
        'INSERT INTO JEUX (titre, type, difficulte, bareme, statut, contenu_json, id_admin)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $req->execute([$titre, $type, $difficulte, $bareme, $statut, $contenu_json, $id_admin]);
}

// ── TESTS ─────────────────────────────────────────────────────────────────────

function lister_tests()
{
    $db = connecter_bdd();
    return $db->query('SELECT * FROM TEST ORDER BY date_creation DESC')->fetchAll();
}

function trouver_test_par_id($id)
{
    $db  = connecter_bdd();
    $req = $db->prepare('SELECT * FROM TEST WHERE id_test = ?');
    $req->execute([$id]);
    return $req->fetch();
}

function lister_jeux_du_test($id_test)
{
    $db  = connecter_bdd();
    $req = $db->prepare(
        'SELECT j.* FROM JEUX j
         INNER JOIN TEST_JEUX tj ON j.id_jeux = tj.id_jeux
         WHERE tj.id_test = ?
         ORDER BY tj.position'
    );
    $req->execute([$id_test]);
    return $req->fetchAll();
}

function creer_test_bdd($titre, $duree, $id_admin)
{
    $db  = connecter_bdd();
    $req = $db->prepare(
        'INSERT INTO TEST (titre, duree, id_admin) VALUES (?, ?, ?)'
    );
    $req->execute([$titre, (int)$duree, (int)$id_admin]);
    return $db->lastInsertId();
}

function lier_jeux_test($id_test, array $jeux)
{
    $db  = connecter_bdd();
    $req = $db->prepare(
        'INSERT INTO TEST_JEUX (id_test, id_jeux, position, points_max) VALUES (?, ?, ?, 1)'
    );
    foreach ($jeux as $position => $id_jeux) {
        $req->execute([$id_test, (int)$id_jeux, $position + 1]);
    }
}

// ── CANDIDATS ─────────────────────────────────────────────────────────────────

function lister_candidats()
{
    $db = connecter_bdd();
    return $db->query('SELECT * FROM CANDIDAT ORDER BY nom, prenom')->fetchAll();
}

function attribuer_test_a_candidat($id_test, $id_candidat)
{
    $db  = connecter_bdd();
    $req = $db->prepare('UPDATE CANDIDAT SET id_test = ? WHERE id_candidat = ?');
    $req->execute([$id_test, $id_candidat]);
}
