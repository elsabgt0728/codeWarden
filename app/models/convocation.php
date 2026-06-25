<?php

function convocation_creer($id_candidat, $id_session, $lien_acces, $date_expiration = null)
{
    $db = connecter_bdd();

    $sql = "
        INSERT INTO CONVOCATION (lien_acces, date_expiration, id_candidat, id_session)
        VALUES (?, ?, ?, ?)
    ";

    $stmt = $db->prepare($sql);
    return $stmt->execute([$lien_acces, $date_expiration, $id_candidat, $id_session]);
}
