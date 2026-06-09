<?php
// Affiche un fichier de vue en lui transmettant des variables
// Ex: afficher_vue('auth/login', ['error' => 'Oops']) → $error = 'Oops' dans la vue
function afficher_vue($vue, $donnees = [])
{
    extract($donnees);
    require ROOT . '/app/views/' . $vue . '.php';
}
