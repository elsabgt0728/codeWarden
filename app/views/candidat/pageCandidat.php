<?php
// $page est toujours fourni par le controller
$page = $page ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace candidat – CodeWarden</title>
    <?php if ($page === 'dashboard'): ?>
        <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/accesAuTest.css">
        <style>
            #modal-profil {
                position: fixed; top: 0; left: 0; right: 0; bottom: 0;
                background: rgba(0,0,0,0.42);
                display: flex; align-items: stretch; justify-content: flex-end;
                z-index: 9999;
            }
            @keyframes slideFromRight { from { transform: translateX(100%); } to { transform: translateX(0); } }
            #modal-profil .modal-card {
                width: 88%; max-width: 360px; height: 100%;
                background: #fff; display: flex; flex-direction: column;
                overflow-y: auto; border-radius: 20px 0 0 20px;
                box-shadow: -6px 0 28px rgba(0,0,0,0.20);
                animation: slideFromRight 0.28s ease forwards; color: #1f2933;
            }
            #modal-profil .modal-header { display: flex; align-items: center; gap: 0.9rem; padding: 1.4rem 1.5rem 1.1rem; border-bottom: 1px solid #e9edf2; }
            #modal-profil .modal-avatar { width: 52px; height: 52px; border-radius: 50%; background: #0091e6; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; font-weight: 700; flex-shrink: 0; }
            #modal-profil .modal-header-name { display: block; font-weight: 700; font-size: 0.95rem; color: #1f2933; }
            #modal-profil .modal-header-sub { display: block; font-size: 0.75rem; color: #6b7280; margin-top: 0.1rem; word-break: break-all; }
            #modal-profil .modal-body { padding: 1.4rem 1.5rem; flex: 1; display: flex; flex-direction: column; }
            #modal-profil .modal-body h2 { font-size: 0.78rem; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.07em; margin: 0 0 1.2rem; }
            #modal-profil .modal-group { display: flex; flex-direction: column; margin-bottom: 1rem; }
            #modal-profil .modal-group label { font-size: 0.82rem; font-weight: 600; color: #374151; margin-bottom: 0.3rem; }
            #modal-profil .modal-group input { padding: 0.65rem 0.85rem; border: 1.5px solid #d0d7e2; border-radius: 10px; font-size: 0.93rem; background: #f8fbff; color: #1f2933; outline: none; transition: border 0.2s, box-shadow 0.2s; width: 100%; box-sizing: border-box; }
            #modal-profil .modal-group input:focus { border-color: #00a2ff; box-shadow: 0 0 0 3px rgba(0,162,255,0.18); }
            #modal-profil .modal-error { color: #d62828; font-size: 0.75rem; margin-top: 0.2rem; min-height: 1em; }
            #modal-profil .msg-profil { padding: 0.65rem 0.9rem; border-radius: 10px; font-size: 0.85rem; font-weight: 600; margin-bottom: 1rem; text-align: center; }
            #modal-profil .msg-profil--ok  { background:#e5ffe9; color:#0f7a2a; border:1px solid #8fe3a1; }
            #modal-profil .msg-profil--err { background:#ffe5e5; color:#a80000; border:1px solid #ff9b9b; }
            #modal-profil .modal-footer { padding: 1rem 1.5rem 1.5rem; display: flex; flex-direction: column; gap: 0.6rem; border-top: 1px solid #e9edf2; }
            #modal-profil .modal-btn-save { width: 100%; padding: 0.85rem; background: #00a2ff; color: #fff; border: none; border-radius: 12px; font-size: 0.95rem; font-weight: 700; cursor: pointer; transition: background 0.2s; }
            #modal-profil .modal-btn-save:hover { background: #008ed1; }
            #modal-profil .modal-btn-retour { width: 100%; padding: 0.8rem; background: #f3f4f6; color: #374151; border: none; border-radius: 12px; font-size: 0.93rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
            #modal-profil .modal-btn-retour:hover { background: #e5e7eb; }
        </style>
    <?php elseif ($page === 'test'): ?>
        <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/progression.css">
        <style>
            .question { display: none; }
            .question.visible { display: block; }
            .propositions { display: flex; flex-direction: column; gap: 12px; margin-top: 16px; }
            .proposition-label { display: flex; align-items: center; gap: 12px; padding: 12px 16px; background: rgba(255,255,255,0.12); border: 1.5px solid rgba(255,255,255,0.25); border-radius: 10px; cursor: pointer; transition: background 0.2s; }
            .proposition-label:hover { background: rgba(255,255,255,0.22); }
            .proposition-label input[type="radio"] { accent-color: #00a2ff; width: 18px; height: 18px; flex-shrink: 0; }
            .proposition-label img { max-width: 160px; border-radius: 8px; }
            .question-intitule { font-size: 1.1rem; font-weight: 600; margin-bottom: 4px; }
        </style>
    <?php else: ?>
        <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/testTermine.css">
    <?php endif; ?>
</head>
<body>

<?php if ($page === 'dashboard'): ?>

    <header>
        <div class="partgauche">
            <div class="logo"><img src="<?= BASE_URL ?>/public/assets/images/CWL.png" alt="Logo CodeWarden"></div>
            <div class="textheader">
                <h1>Plateforme admission</h1>
                <h2>Espace candidat</h2>
            </div>
        </div>
        <div class="partdroite">
            <div class="infouser">
                <span>Connecté en tant que </span>
                <strong><?= htmlspecialchars(trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? ''))) ?></strong>
            </div>
            <button type="button" class="btn-header" id="btn-ouvrir-profil">Mon profil</button>
            <a class="btn-header btn-header--deconnexion" href="<?= BASE_URL ?>/logout">Déconnexion</a>
        </div>
    </header>

    <main>
        <div class="titre">
            <div class="namespace">Dashboard candidat</div>
            <div class="phraseintro">Bienvenue, consultez votre test d'admission et suivez votre progression.</div>
        </div>

        <?php
        $pt = $passage_termine ?? null;
        $decision = $pt ? ($pt['decision'] ?? 'en_attente') : null;

        $dec_label = match($decision) {
            'admis'         => 'Admis(e)',
            'refuse'        => 'Refusé(e)',
            'liste_attente' => 'Liste d\'attente',
            'en_attente'    => 'En cours de traitement',
            default         => null,
        };
        $dec_color = match($decision) {
            'admis'         => '#16a34a',
            'refuse'        => '#dc2626',
            'liste_attente' => '#d97706',
            default         => '#6b7280',
        };
        $dec_bg = match($decision) {
            'admis'         => '#dcfce7',
            'refuse'        => '#fee2e2',
            'liste_attente' => '#fef9c3',
            default         => '#f3f4f6',
        };
        ?>

        <?php if ($pt): ?>
        <!-- Résultat déjà disponible -->
        <div class="card" style="border-left: 5px solid <?= $dec_color ?>;">
            <div class="entete">
                <div class="logo"><img src="<?= BASE_URL ?>/public/assets/images/iconeFeuille.png" alt=""></div>
                <div class="textcard">
                    <h3>Votre résultat</h3>
                    <h4><?= htmlspecialchars($pt['titre_test'] ?? 'Test d\'admission') ?></h4>
                </div>
            </div>
            <div class="autorisation" style="background:<?= $dec_bg ?>; border-radius:12px; padding:14px 18px; margin:16px 0;">
                <div class="textauto">
                    <h3 style="color:<?= $dec_color ?>; font-size:1.2rem;">
                        <?= $dec_label ?>
                    </h3>
                    <?php if ($decision === 'en_attente'): ?>
                    <h4>La décision de l'établissement est en cours. Revenez consulter dans quelques jours.</h4>
                    <?php elseif ($decision === 'admis'): ?>
                    <h4>Félicitations ! Vous avez été admis(e). Vous recevrez un e-mail de confirmation.</h4>
                    <?php elseif ($decision === 'refuse'): ?>
                    <h4>Votre candidature n'a pas été retenue cette année.</h4>
                    <?php else: ?>
                    <h4>Vous êtes en liste d'attente. Nous vous contacterons si une place se libère.</h4>
                    <?php endif; ?>
                </div>
            </div>
            <div class="informations" style="margin-top:0;">
                <div class="inforow">
                    <span class="label">Test passé le</span>
                    <span class="value">
                        <?= $pt['date_fin'] ? date('d/m/Y à H:i', strtotime($pt['date_fin'])) : '–' ?>
                    </span>
                </div>
                <div class="inforow">
                    <span class="label">Référence</span>
                    <span class="value">TEST-<?= date('Y') ?>-<?= str_pad($pt['id_passage_test'], 4, '0', STR_PAD_LEFT) ?></span>
                </div>
            </div>
        </div>

        <?php elseif ($test_info): ?>
        <!-- Test disponible -->
        <div class="card">
            <div class="entete">
                <div class="logo"><img src="<?= BASE_URL ?>/public/assets/images/iconeFeuille.png" alt=""></div>
                <div class="textcard">
                    <h3>Accéder au test</h3>
                    <h4><?= htmlspecialchars($test_info['titre']) ?></h4>
                </div>
            </div>
            <div class="autorisation">
                <div class="coche"><div class="icone"><img src="<?= BASE_URL ?>/public/assets/images/iconeCoche.png" alt=""></div></div>
                <div class="textauto">
                    <h3>Accès autorisé</h3>
                    <h4>Vous pouvez commencer votre test d'admission.</h4>
                </div>
            </div>
            <a class="btnStart" href="<?= BASE_URL ?>/candidat/commencer?id_test=<?= $test_info['id_test'] ?>" role="button">
                Passer le test <span class="arrow">→</span>
            </a>
        </div>
        <div class="informations">
            <h3>Informations</h3>
            <div class="inforow">
                <span class="label">Durée du test</span>
                <span class="value"><?= (int)$test_info['duree_minutes'] ?> minutes</span>
            </div>
            <div class="inforow">
                <span class="label">Type</span>
                <span class="value">Jeu de logique</span>
            </div>
        </div>

        <?php else: ?>
        <!-- Aucun test -->
        <div class="card">
            <div class="entete">
                <div class="logo"><img src="<?= BASE_URL ?>/public/assets/images/iconeFeuille.png" alt=""></div>
                <div class="textcard">
                    <h3>Aucun test disponible</h3>
                    <h4>Aucune session active pour le moment.</h4>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <footer></footer>

<?php elseif ($page === 'test'): ?>

    <?php
    $bridge   = '<script>window.CodeWarden={submit:function(s){window.parent.postMessage({type:"cw_result",score:Number(s)},"*");}};</script>';
    $jeux_js  = array_map(function($j) use ($bridge) {
        return ['id' => (int)$j['id_jeux'], 'titre' => $j['titre'], 'bareme' => (int)$j['bareme'], 'html' => $bridge . $j['contenu_html']];
    }, $jeux_liste ?? []);
    $nb_jeux  = count($jeux_js);
    ?>

    <header>
        <div class="entete-text">
            <h1>Test d'admission</h1>
            <?php if ($nb_jeux > 1): ?>
            <span style="font-size:13px;opacity:.8;">
                Exercice <span id="jeu-courant">1</span> / <?= $nb_jeux ?>
            </span>
            <?php endif; ?>
        </div>
        <div class="timer" id="timer" role="timer" aria-live="polite" aria-label="Temps restant">
            <?= (int)$duree ?>:00
        </div>
    </header>

    <script>
        const JEUX             = <?= json_encode($jeux_js, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>;
        const TEST_DURATION    = <?= (int)$duree ?> * 60;
        const TEMPS_ECOULE     = <?= (int)($temps_ecoule ?? 0) ?>;
        const ID_PASSAGE       = <?= (int)($id_passage ?? 0) ?>;
        const ID_TEST          = <?= (int)($id_test ?? 0) ?>;
        const BASE_URL         = "<?= BASE_URL ?>";
        const SCORES_EXISTANTS = <?= json_encode(array_map(function($r) {
            return ['id_jeux' => (int)$r['id_jeux'], 'score' => (float)$r['points_obtenus']];
        }, $scores_existants ?? [])) ?>;
    </script>

    <main style="position:relative;">
        <!-- Overlay de transition (par-dessus l'iframe) -->
        <div id="transition-screen"
             style="display:none;position:absolute;inset:0;z-index:10;
                    background:#fff;border-radius:16px;
                    flex-direction:column;align-items:center;justify-content:center;
                    text-align:center;padding:48px 32px;">
            <div style="font-size:56px;margin-bottom:16px;">✅</div>
            <div id="transition-titre" style="font-size:22px;font-weight:800;color:#1f2933;margin-bottom:8px;"></div>
            <div id="transition-sub" style="font-size:14px;color:#6b7280;margin-bottom:36px;"></div>
            <button id="btn-suivant"
                style="padding:15px 40px;background:linear-gradient(135deg,#00a2ff,#0057b8);color:#fff;
                       border:none;border-radius:14px;font-size:16px;font-weight:700;cursor:pointer;
                       letter-spacing:.04em;transition:opacity .2s;">
            </button>
        </div>

        <iframe id="game-frame"
            sandbox="allow-scripts allow-forms"
            style="width:100%;height:80vh;border:none;border-radius:16px;background:#fff;display:block;">
        </iframe>
    </main>

    <footer></footer>

<?php elseif ($page === 'finish'): ?>

    <?php
    $date_fin = $passage['date_fin'] ?? null;
    $date_obj = $date_fin ? new DateTime($date_fin) : null;
    $reference = 'TEST-' . date('Y') . '-' . str_pad($passage['id_passage_test'] ?? 0, 4, '0', STR_PAD_LEFT);
    $decision  = $resultat['decision'] ?? 'en_attente';
    $decision_label = match($decision) {
        'admis'        => 'Admis',
        'refuse'       => 'Refusé',
        'liste_attente'=> 'Liste d\'attente',
        default        => 'En traitement',
    };
    $decision_color = match($decision) {
        'admis'  => '#16A34A',
        'refuse' => '#DC2626',
        default  => '#D97706',
    };
    ?>

    <div class="container">
        <div class="success-card">
            <div class="success-icon">✓</div>
            <div class="success-title">Test terminé avec succès</div>
            <div class="success-subtitle">
                Votre test d'admission a été soumis et enregistré.
                Vous pouvez maintenant quitter cette page en toute sécurité.
            </div>

            <div class="info-box">
                <div class="info-row">
                    <div class="logo"><img src="<?= BASE_URL ?>/public/assets/images/check.png" alt=""></div>
                    <div class="info-text">
                        <h3>Votre test est enregistré</h3>
                        <p>Toutes vos réponses ont été sauvegardées et sont en cours de traitement.</p>
                    </div>
                </div>
                <div class="info-row">
                    <div class="logo"><img src="<?= BASE_URL ?>/public/assets/images/enveloppe.png" alt=""></div>
                    <div class="info-text">
                        <h3>Résultats par email</h3>
                        <p>Vous recevrez vos résultats par email dans un délai de 48 heures maximum.</p>
                    </div>
                </div>
            </div>

            <div class="detail-box">
                <div>
                    <div class="label">Date de soumission</div>
                    <div class="value"><?= $date_obj ? $date_obj->format('d/m/Y') : '–' ?></div>
                </div>
                <div>
                    <div class="label">Heure de soumission</div>
                    <div class="value"><?= $date_obj ? $date_obj->format('H:i') : '–' ?></div>
                </div>
                <div>
                    <div class="label">Numéro de référence</div>
                    <div class="value"><?= htmlspecialchars($reference) ?></div>
                </div>
                <div>
                    <div class="label">Statut</div>
                    <div class="value" style="color: <?= $decision_color ?>;"><?= $decision_label ?></div>
                </div>
            </div>

            <div class="btn-box">
                <a class="btnStart" href="<?= BASE_URL ?>/logout">
                    <div class="logo"><img src="<?= BASE_URL ?>/public/assets/images/deconnexion1.png" alt=""></div>
                    Quitter la page
                </a>
            </div>

            <div class="footer-text">
                Vous pouvez fermer cette fenêtre ou retourner à votre tableau de bord
            </div>
        </div>
    </div>

<?php endif; ?>

<script>
    const testData = {
        questions: <?= json_encode($questions ?? [], JSON_UNESCAPED_UNICODE) ?>
    };
</script>

<!-- PANNEAU PROFIL (slide depuis la droite) -->
<div class="modal-overlay" id="modal-profil" style="display:none" role="dialog" aria-modal="true" aria-labelledby="modal-titre">
    <div class="modal-card">
        <div class="modal-header">
            <div class="modal-avatar" aria-hidden="true">
                <?= strtoupper(mb_substr($user['prenom'] ?? '?', 0, 1)) ?>
            </div>
            <div class="modal-header-info">
                <span class="modal-header-name"><?= htmlspecialchars(trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? ''))) ?></span>
                <span class="modal-header-sub"><?= htmlspecialchars($user['email'] ?? '') ?></span>
            </div>
        </div>
        <div class="modal-body">
            <h2 id="modal-titre">Modifier le profil</h2>
            <div id="msg-profil" class="msg-profil" style="display:none"></div>
            <form id="form-profil" novalidate>
                <div class="modal-group">
                    <label for="m-prenom">Prénom</label>
                    <input type="text" id="m-prenom" name="prenom" value="<?= htmlspecialchars($user['prenom'] ?? '') ?>">
                    <span class="modal-error" id="err-prenom"></span>
                </div>
                <div class="modal-group">
                    <label for="m-nom">Nom</label>
                    <input type="text" id="m-nom" name="nom" value="<?= htmlspecialchars($user['nom'] ?? '') ?>">
                    <span class="modal-error" id="err-nom"></span>
                </div>
                <div class="modal-group">
                    <label for="m-email">E-mail</label>
                    <input type="email" id="m-email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                    <span class="modal-error" id="err-email"></span>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="submit" form="form-profil" class="modal-btn-save">Enregistrer</button>
            <button type="button" class="modal-btn-retour" id="btn-fermer-profil">Retour</button>
        </div>
    </div>
</div>

<script>window.BASE_URL = '<?= BASE_URL ?>';</script>
<script src="<?= BASE_URL ?>/public/assets/js/candidat.js"></script>
<script src="<?= BASE_URL ?>/public/assets/js/test.js"></script>
</body>
</html>
