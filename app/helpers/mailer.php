<?php

require_once ROOT . '/vendor/phpmailer/src/Exception.php';
require_once ROOT . '/vendor/phpmailer/src/PHPMailer.php';
require_once ROOT . '/vendor/phpmailer/src/SMTP.php';
require_once ROOT . '/config/mail.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Pause (en millisecondes) entre deux envois d'un même lot, pour ne pas
// déclencher le throttling anti-spam du serveur SMTP (Gmail notamment).
const MAILER_DELAI_LOT_MS = 200;

function envoyer_decision($candidat_email, $candidat_prenom, $candidat_nom, $titre_test, $decision)
{
    $labels = [
        'admis'         => ['label' => 'Admis(e)',             'couleur' => '#0f7a2a', 'bg' => '#e5ffe9', 'message' => 'Félicitations ! Votre candidature a été retenue.'],
        'refuse'        => ['label' => 'Refusé(e)',            'couleur' => '#a80000', 'bg' => '#ffe5e5', 'message' => 'Après examen de votre dossier, votre candidature n\'a pas été retenue cette fois-ci.'],
        'liste_attente' => ['label' => 'Liste d\'attente',     'couleur' => '#92580a', 'bg' => '#fff4e5', 'message' => 'Votre candidature est placée sur liste d\'attente.'],
        'en_attente'    => ['label' => 'En cours de traitement','couleur' => '#1e40af', 'bg' => '#eff6ff', 'message' => 'Votre dossier est en cours d\'examen.'],
    ];

    $info = $labels[$decision] ?? $labels['en_attente'];

    $mail = _mailer_ouvrir_connexion();
    $mail->SMTPKeepAlive = false; // envoi isolé : pas besoin de garder la connexion ouverte

    try {
        $mail->addAddress($candidat_email, trim($candidat_prenom . ' ' . $candidat_nom));

        $mail->isHTML(true);
        $mail->Subject = 'Résultat de votre candidature – CodeWarden';
        $mail->Body    = '
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f4f6fb;font-family:Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6fb;padding:40px 0;">
    <tr><td align="center">
      <table width="560" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">

        <tr>
          <td style="background:#0091e6;padding:32px 40px;text-align:center;">
            <h1 style="margin:0;color:#ffffff;font-size:26px;letter-spacing:1px;">CodeWarden</h1>
            <p style="margin:6px 0 0;color:#cce9ff;font-size:14px;">Plateforme d\'admission</p>
          </td>
        </tr>

        <tr>
          <td style="padding:36px 40px;">
            <p style="margin:0 0 16px;font-size:16px;color:#1f2933;">Bonjour <strong>' . htmlspecialchars($candidat_prenom) . '</strong>,</p>
            <p style="margin:0 0 24px;font-size:15px;color:#374151;line-height:1.6;">
              Suite à votre participation au test <strong>' . htmlspecialchars($titre_test) . '</strong>, voici le résultat de votre candidature :
            </p>

            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td align="center" style="padding:24px 0;">
                  <table cellpadding="0" cellspacing="0">
                    <tr>
                      <td bgcolor="' . $info['bg'] . '"
                          style="border-radius:12px;padding:20px 40px;text-align:center;border:2px solid ' . $info['couleur'] . ';">
                        <p style="margin:0;font-size:22px;font-weight:700;color:' . $info['couleur'] . ';">' . $info['label'] . '</p>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>

            <p style="margin:8px 0 0;font-size:15px;color:#374151;line-height:1.6;text-align:center;">
              ' . $info['message'] . '
            </p>
          </td>
        </tr>

        <tr>
          <td style="background:#f9fafb;padding:20px 40px;text-align:center;border-top:1px solid #e9edf2;">
            <p style="margin:0;font-size:12px;color:#9ca3af;">
              Cet e-mail a été envoyé automatiquement par la plateforme CodeWarden.<br>
              Merci de ne pas y répondre.
            </p>
          </td>
        </tr>

      </table>
    </td></tr>
  </table>
</body>
</html>';

        $mail->AltBody = "Bonjour $candidat_prenom,\n\nRésultat de votre candidature pour le test \"$titre_test\" : {$info['label']}.\n\n{$info['message']}\n\nCodeWarden";

        $mail->send();
        $mail->smtpClose();
        return true;
    } catch (Exception $e) {
        error_log('[CodeWarden] Mailer decision error — Message: ' . $e->getMessage() . ' | SMTP: ' . $mail->ErrorInfo . ' | To: ' . $candidat_email);
        $mail->smtpClose();
        return false;
    }
}

// ----------------------------------------------------------------------
// Convocations — connexion SMTP réutilisable pour l'envoi en lot
// ----------------------------------------------------------------------

/**
 * Ouvre et configure une connexion SMTP, prête à envoyer (From déjà fixé).
 * Le SMTPKeepAlive est activé par défaut : à utiliser pour plusieurs envois
 * successifs sur la même connexion. L'appelant doit fermer la connexion
 * avec $mail->smtpClose() une fois terminé.
 */
function _mailer_ouvrir_connexion(): PHPMailer
{
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host          = MAIL_HOST;
    $mail->SMTPAuth      = true;
    $mail->Username      = MAIL_USERNAME;
    $mail->Password      = MAIL_PASSWORD;
    $mail->SMTPSecure    = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port          = MAIL_PORT;
    $mail->CharSet       = 'UTF-8';
    $mail->SMTPKeepAlive = true;

    $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);

    return $mail;
}

function _mailer_corps_convocation($candidat_prenom, $titre_test, $duree_minutes, $lien_test, $date_expiration = null): array
{
    $html = '
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f4f6fb;font-family:Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6fb;padding:40px 0;">
    <tr><td align="center">
      <table width="560" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">

        <tr>
          <td style="background:#0091e6;padding:32px 40px;text-align:center;">
            <h1 style="margin:0;color:#ffffff;font-size:26px;letter-spacing:1px;">CodeWarden</h1>
            <p style="margin:6px 0 0;color:#cce9ff;font-size:14px;">Plateforme d\'admission</p>
          </td>
        </tr>

        <tr>
          <td style="padding:36px 40px;">
            <p style="margin:0 0 16px;font-size:16px;color:#1f2933;">Bonjour <strong>' . htmlspecialchars($candidat_prenom) . '</strong>,</p>
            <p style="margin:0 0 24px;font-size:15px;color:#374151;line-height:1.6;">
              Vous êtes convoqué(e) pour passer le test d\'admission <strong>' . htmlspecialchars($titre_test) . '</strong>.
            </p>

            <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f7ff;border-radius:10px;margin-bottom:28px;">
              <tr>
                <td style="padding:20px 24px;">
                  <p style="margin:0 0 8px;font-size:13px;color:#6b7280;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Détails du test</p>
                  <p style="margin:0 0 4px;font-size:15px;color:#1f2933;"><strong>Test :</strong> ' . htmlspecialchars($titre_test) . '</p>
                  <p style="margin:0 0 4px;font-size:15px;color:#1f2933;"><strong>Durée :</strong> ' . (int)$duree_minutes . ' minutes</p>
                  ' . ($date_expiration ? '<p style="margin:4px 0 0;font-size:15px;color:#d62828;"><strong>Lien valable jusqu\'au :</strong> ' . htmlspecialchars($date_expiration) . '</p>' : '') . '
                </td>
              </tr>
            </table>

            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td align="center">
                  <table cellpadding="0" cellspacing="0" border="0">
                    <tr>
                      <td align="center" bgcolor="#0091e6"
                          style="border-radius:10px;mso-padding-alt:0;">
                        <a href="' . $lien_test . '"
                           target="_blank"
                           style="display:inline-block;background:#0091e6;color:#ffffff;
                                  text-decoration:none;font-family:Arial,sans-serif;
                                  font-size:16px;font-weight:700;line-height:1;
                                  padding:14px 36px;border-radius:10px;
                                  mso-padding-alt:14px 36px;">
                          Acc&#233;der au test &#8594;
                        </a>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>

            <p style="margin:28px 0 0;font-size:13px;color:#9ca3af;line-height:1.5;">
              Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :<br>
              <a href="' . $lien_test . '" style="color:#0091e6;word-break:break-all;">' . htmlspecialchars($lien_test) . '</a>
            </p>
          </td>
        </tr>

        <tr>
          <td style="background:#f9fafb;padding:20px 40px;text-align:center;border-top:1px solid #e9edf2;">
            <p style="margin:0;font-size:12px;color:#9ca3af;">
              Cet e-mail a été envoyé automatiquement par la plateforme CodeWarden.<br>
              Merci de ne pas y répondre.
            </p>
          </td>
        </tr>

      </table>
    </td></tr>
  </table>
</body>
</html>';

    $alt = "Bonjour $candidat_prenom,\n\nVous êtes convoqué(e) pour le test : $titre_test (durée : {$duree_minutes} min).\n\nAccédez au test ici : $lien_test\n\nCodeWarden";

    return [$html, $alt];
}

/**
 * Envoie une convocation à un seul candidat sur une connexion déjà ouverte.
 * Ne ferme PAS la connexion — à la charge de l'appelant (permet de l'appeler
 * en boucle sur la même connexion SMTP).
 */
function _envoyer_convocation_sur_connexion(PHPMailer $mail, $candidat_email, $candidat_prenom, $candidat_nom, $titre_test, $duree_minutes, $lien_test, $date_expiration = null): bool
{
    try {
        $mail->clearAddresses();
        $mail->clearAttachments();
        $mail->addAddress($candidat_email, trim($candidat_prenom . ' ' . $candidat_nom));

        $mail->isHTML(true);
        $mail->Subject = 'Convocation – Test d\'admission CodeWarden';
        [$mail->Body, $mail->AltBody] = _mailer_corps_convocation($candidat_prenom, $titre_test, $duree_minutes, $lien_test, $date_expiration);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('[CodeWarden] Mailer convocation error — ' . $mail->ErrorInfo . ' | To: ' . $candidat_email);
        return false;
    }
}

/**
 * Envoi isolé d'une convocation (ouvre et ferme sa propre connexion).
 * Conservée pour compatibilité ; préférer envoyer_convocations_lot() pour
 * plusieurs candidats d'un coup.
 */
function envoyer_convocation($candidat_email, $candidat_prenom, $candidat_nom, $titre_test, $duree_minutes, $lien_test, $date_expiration = null)
{
    $mail = _mailer_ouvrir_connexion();
    $mail->SMTPKeepAlive = false;
    $ok = _envoyer_convocation_sur_connexion($mail, $candidat_email, $candidat_prenom, $candidat_nom, $titre_test, $duree_minutes, $lien_test, $date_expiration);
    $mail->smtpClose();
    return $ok;
}

/**
 * Envoie les convocations de TOUT un lot de candidats sur UNE SEULE connexion
 * SMTP (au lieu d'en ouvrir une par candidat). Beaucoup plus rapide et moins
 * susceptible de déclencher le throttling anti-spam du serveur mail sur de
 * grosses promotions.
 *
 * @param array  $convocations    liste de ['email'=>, 'prenom'=>, 'nom'=>, 'lien'=>, ...]
 *                                — 'lien' est désormais PERSONNEL à chaque
 *                                candidat (contient son propre jeton), donc
 *                                calculé par l'appelant avant ce lot, pas ici.
 * @param string $titre_test
 * @param int    $duree_minutes
 * @param ?string $date_expiration
 * @return array ['ok' => [emails envoyés], 'echecs' => [emails en échec]]
 */
function envoyer_convocations_lot(array $convocations, $titre_test, $duree_minutes, $date_expiration = null): array
{
    $resultat = ['ok' => [], 'echecs' => []];

    if (empty($convocations)) {
        return $resultat;
    }

    $mail = _mailer_ouvrir_connexion();

    foreach ($convocations as $conv) {
        $ok = _envoyer_convocation_sur_connexion(
            $mail,
            $conv['email'],
            $conv['prenom'],
            $conv['nom'],
            $titre_test,
            $duree_minutes,
            $conv['lien'],
            $date_expiration
        );

        if ($ok) {
            $resultat['ok'][] = $conv['email'];
        } else {
            $resultat['echecs'][] = $conv['email'];
        }

        if (MAILER_DELAI_LOT_MS > 0) {
            usleep(MAILER_DELAI_LOT_MS * 1000);
        }
    }

    $mail->smtpClose();

    return $resultat;
}
