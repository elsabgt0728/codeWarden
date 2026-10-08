<?php

$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$url = str_replace(BASE_URL, '', $url);
$url = str_replace('/index.php', '', $url);
if ($url === '' || $url === false) $url = '/';

$methode = $_SERVER['REQUEST_METHOD'];
$cle     = $methode . ' ' . $url;

$routes = [
    // Auth candidat
    'GET /login'    => 'page_connexion',
    'POST /login'   => 'traiter_connexion',
    'GET /signup'   => 'page_inscription',
    'POST /signup'  => 'traiter_inscription',
    'GET /logout'   => 'deconnecter',
    'GET /profile'  => 'page_profil',
    'POST /profile' => 'modifier_profil',
    'GET /'         => 'page_accueil',

    // Auth admin
    'GET /admin/login'  => 'page_connexion_admin',
    'POST /admin/login' => 'traiter_connexion_admin',
    'GET /admin/logout' => 'deconnecter_admin',
    'GET /admin'        => 'tableau_de_bord',

    // Candidat
    'GET /candidat'             => 'page_candidat',
    'GET /candidat/commencer'   => 'candidat_commencer_test',
    'POST /candidat/terminer'   => 'candidat_terminer_test',
    'POST /candidat/score-jeu'  => 'candidat_enregistrer_score_jeu',
    'POST /candidat/expirer'    => 'candidat_expire_test',

    // Exercices (admin)
    'POST /admin/jeux/creer'     => 'traiter_creation_jeux',
    'GET /admin/jeux/modifier'   => 'page_modifier_jeux',
    'POST /admin/jeux/modifier'  => 'traiter_modification_jeux',
    'POST /admin/jeux/supprimer' => 'traiter_suppression_jeux',

    // Tests (admin)
    'POST /admin/test/creer'      => 'traiter_creation_test',
    'GET /admin/test/modifier'    => 'page_modifier_test',
    'POST /admin/test/modifier'   => 'traiter_modification_test',
    'POST /admin/test/supprimer'  => 'traiter_suppression_test',
    'POST /admin/test/convoquer'  => 'admin_convoquer_test',

    // Décision candidat (admin)
    'POST /admin/candidat/decision' => 'admin_decision_candidat',

    // Groupes (admin)
    'POST /admin/groupe/creer'            => 'traiter_creation_groupe',
    'POST /admin/groupe/supprimer'        => 'traiter_suppression_groupe',
    'POST /admin/groupe/candidat/ajouter' => 'traiter_ajout_candidat_groupe',
    'POST /admin/groupe/candidat/retirer' => 'traiter_retrait_candidat_groupe',
];

if (isset($routes[$cle])) {
    $routes[$cle]();
    exit;
}

// Routes dynamiques avec paramètres (regex)
if ($cle === 'GET /api/stats/admin/global')      { api_stats_admin_global();      exit; }
if ($cle === 'GET /api/stats/admin/comparaison') { api_stats_admin_comparaison(); exit; }

if (preg_match('#^GET /api/stats/candidat/(\d+)$#', $cle, $m))      { api_stats_candidat((int)$m[1]);      exit; }
if (preg_match('#^GET /api/stats/admin/epreuve/(\d+)$#', $cle, $m)) { api_stats_admin_epreuve((int)$m[1]); exit; }

http_response_code(404);
echo '<h1>404 – Page non trouvée</h1>';
