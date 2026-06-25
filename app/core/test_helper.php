<?php
function calcul_score($questions, $reponses)
{
    $score = 0;

    foreach ($questions as $index => $q) {
        $bonne = $q["bonne_reponse"];

        if (isset($reponses[$index]) && $reponses[$index] == $bonne) {
            $score++;
        }
    }

    return $score;
}
