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

if      ($methode === 'GET'  && $url === '/')             page_connexion();
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
elseif  ($methode === 'GET'  && $url === '/candidat')     page_candidat();
elseif ($methode === 'POST' && $url === '/admin/jeux/creer') traiter_creation_jeux();


else {
    http_response_code(404);
    echo '<h1>404 – Page non trouvée</h1>';
}
