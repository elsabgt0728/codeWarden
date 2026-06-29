<?php
// Calcule l'URL demandée sans le préfixe /codewarden
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// enlever /codewarden/public
$url = str_replace(BASE_URL, '', $url);

// enlever index.php
$url = str_replace('/index.php', '', $url);

// si vide → racine
if ($url === '' || $url === false) {
    $url = '/';
}

$methode = $_SERVER['REQUEST_METHOD'];

if      ($methode === 'GET'  && $url === '/')             page_accueil();
elseif  ($methode === 'GET'  && $url === '/login')        page_connexion();
elseif  ($methode === 'POST' && $url === '/login')        traiter_connexion();
elseif  ($methode === 'GET'  && $url === '/signup')       page_inscription();
elseif  ($methode === 'POST' && $url === '/signup')       traiter_inscription();
elseif  ($methode === 'GET'  && $url === '/logout')       deconnecter();
elseif  ($methode === 'GET'  && $url === '/profile')      page_profil();
elseif  ($methode === 'POST' && $url === '/profile')      modifier_profil();
elseif  ($methode === 'GET'  && $url === '/admin/login')  page_connexion_admin();
elseif  ($methode === 'POST' && $url === '/admin/login')  traiter_connexion_admin();
elseif  ($methode === 'GET'  && $url === '/admin/logout') deconnecter_admin();
elseif  ($methode === 'GET'  && $url === '/admin')        tableau_de_bord();
elseif  ($methode === 'GET'  && $url === '/candidat')          page_candidat();
elseif ($methode === 'GET'  && $url === '/candidat/commencer') candidat_commencer_test();
elseif ($methode === 'POST' && $url === '/candidat/terminer')   candidat_terminer_test();
elseif ($methode === 'POST' && $url === '/candidat/score-jeu') candidat_enregistrer_score_jeu();
elseif ($methode === 'POST' && $url === '/candidat/expirer')   candidat_expire_test();
elseif ($methode === 'POST' && $url === '/admin/jeux/creer')      traiter_creation_jeux();
elseif ($methode === 'GET'  && $url === '/admin/jeux/modifier')   page_modifier_jeux();
elseif ($methode === 'POST' && $url === '/admin/jeux/modifier')   traiter_modification_jeux();
elseif ($methode === 'POST' && $url === '/admin/jeux/supprimer')  traiter_suppression_jeux();
elseif ($methode === 'POST' && $url === '/admin/test/creer')      traiter_creation_test();
elseif ($methode === 'GET'  && $url === '/admin/test/modifier')   page_modifier_test();
elseif ($methode === 'POST' && $url === '/admin/test/modifier')   traiter_modification_test();
elseif ($methode === 'POST' && $url === '/admin/test/supprimer')  traiter_suppression_test();
elseif ($methode === 'POST' && $url === '/admin/test/convoquer')  admin_convoquer_test();
elseif ($methode === 'POST' && $url === '/admin/candidat/decision') admin_decision_candidat();

//  API stats 
elseif ($methode === 'GET' && preg_match('#^/api/stats/candidat/(\d+)$#', $url, $m))
    api_stats_candidat((int) $m[1]);
elseif ($methode === 'GET' && $url === '/api/stats/admin/global')
    api_stats_admin_global();
elseif ($methode === 'GET' && preg_match('#^/api/stats/admin/epreuve/(\d+)$#', $url, $m))
    api_stats_admin_epreuve((int) $m[1]);
elseif ($methode === 'GET' && $url === '/api/stats/admin/comparaison')
    api_stats_admin_comparaison();

else {
    http_response_code(404);
    echo '<h1>404 – Page non trouvée</h1>';
}
