<?php
$page = $_GET["page"] ?? "dashboard";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace candidat – CodeWarden</title>
      <?php if ($page === "dashboard"): ?>
        <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/accesAuTest.css">
        <style>
            /* ── PANNEAU PROFIL ── */
            #modal-profil {
                position: fixed;
                top: 0; left: 0; right: 0; bottom: 0;
                background: rgba(0,0,0,0.42);
                display: flex;
                align-items: stretch;
                justify-content: flex-end;
                z-index: 9999;
            }
            @keyframes slideFromRight {
                from { transform: translateX(100%); }
                to   { transform: translateX(0); }
            }
            #modal-profil .modal-card {
                width: 88%;
                max-width: 360px;
                height: 100%;
                background: #fff;
                display: flex;
                flex-direction: column;
                overflow-y: auto;
                border-radius: 20px 0 0 20px;
                box-shadow: -6px 0 28px rgba(0,0,0,0.20);
                animation: slideFromRight 0.28s ease forwards;
                color: #1f2933;
            }
            /* En-tête */
            #modal-profil .modal-header {
                display: flex;
                align-items: center;
                gap: 0.9rem;
                padding: 1.4rem 1.5rem 1.1rem;
                border-bottom: 1px solid #e9edf2;
            }
            #modal-profil .modal-avatar {
                width: 52px;
                height: 52px;
                border-radius: 50%;
                background: #0091e6;
                color: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.3rem;
                font-weight: 700;
                flex-shrink: 0;
            }
            #modal-profil .modal-header-name {
                display: block;
                font-weight: 700;
                font-size: 0.95rem;
                color: #1f2933;
            }
            #modal-profil .modal-header-sub {
                display: block;
                font-size: 0.75rem;
                color: #6b7280;
                margin-top: 0.1rem;
                word-break: break-all;
            }
            /* Corps */
            #modal-profil .modal-body {
                padding: 1.4rem 1.5rem;
                flex: 1;
                display: flex;
                flex-direction: column;
            }
            #modal-profil .modal-body h2 {
                font-size: 0.78rem;
                font-weight: 700;
                color: #9ca3af;
                text-transform: uppercase;
                letter-spacing: 0.07em;
                margin: 0 0 1.2rem;
            }
            #modal-profil .modal-group {
                display: flex;
                flex-direction: column;
                margin-bottom: 1rem;
            }
            #modal-profil .modal-group label {
                font-size: 0.82rem;
                font-weight: 600;
                color: #374151;
                margin-bottom: 0.3rem;
            }
            #modal-profil .modal-group input {
                padding: 0.65rem 0.85rem;
                border: 1.5px solid #d0d7e2;
                border-radius: 10px;
                font-size: 0.93rem;
                background: #f8fbff;
                color: #1f2933;
                outline: none;
                transition: border 0.2s, box-shadow 0.2s;
                width: 100%;
                box-sizing: border-box;
            }
            #modal-profil .modal-group input:focus {
                border-color: #00a2ff;
                box-shadow: 0 0 0 3px rgba(0,162,255,0.18);
            }
            #modal-profil .modal-error {
                color: #d62828;
                font-size: 0.75rem;
                margin-top: 0.2rem;
                min-height: 1em;
            }
            #modal-profil .msg-profil {
                padding: 0.65rem 0.9rem;
                border-radius: 10px;
                font-size: 0.85rem;
                font-weight: 600;
                margin-bottom: 1rem;
                text-align: center;
            }
            #modal-profil .msg-profil--ok  { background:#e5ffe9; color:#0f7a2a; border:1px solid #8fe3a1; }
            #modal-profil .msg-profil--err { background:#ffe5e5; color:#a80000; border:1px solid #ff9b9b; }
            /* Pied */
            #modal-profil .modal-footer {
                padding: 1rem 1.5rem 1.5rem;
                display: flex;
                flex-direction: column;
                gap: 0.6rem;
                border-top: 1px solid #e9edf2;
            }
            #modal-profil .modal-btn-save {
                width: 100%;
                padding: 0.85rem;
                background: #00a2ff;
                color: #fff;
                border: none;
                border-radius: 12px;
                font-size: 0.95rem;
                font-weight: 700;
                cursor: pointer;
                transition: background 0.2s;
            }
            #modal-profil .modal-btn-save:hover { background: #008ed1; }
            #modal-profil .modal-btn-retour {
                width: 100%;
                padding: 0.8rem;
                background: #f3f4f6;
                color: #374151;
                border: none;
                border-radius: 12px;
                font-size: 0.93rem;
                font-weight: 600;
                cursor: pointer;
                transition: background 0.2s;
            }
            #modal-profil .modal-btn-retour:hover { background: #e5e7eb; }
        </style>
    <?php elseif ($page === "test"): ?>
        <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/progression.css">
    <?php else: ?>
        <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/testTermine.css">
    <?php endif; ?>
</head>
<body>

<?php
if($page === "dashboard"):
?>
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

    <div class="namespace">
        Dashboard candidat
    </div>
    <div class="phraseintro">
        Bienvenue, consultez votre test d'admission et suivez votre progression.
    </div>

    </div>


    <div class="card">

    <div class="entete">
        
        <div class="logo"><img src="<?= BASE_URL ?>/public/assets/images/iconeFeuille.png" alt=""></div>
    <div class="textcard">
        <h3>Accéder au test</h3>
        <h4>Test d'admission 2026</h4>
    </div>

     </div>


    <div class="autorisation">

    <div class="coche"><div class="icone"><img src="<?= BASE_URL ?>/public/assets/images/iconeCoche.png" alt=""></div> </div>

        <div class="textauto">
        <h3>Accès autorisé</h3>
        <h4>Vous pouvez commencer votre test d'admission.</h4>
        </div>
    </div>

        <a class="btnStart" href="<?= BASE_URL ?>/candidat?page=test" role="button">Passer le test
        <span class="arrow">→</span>
        </a>
</div>


    <div class="informations">

    <h3>Informations</h3>

    <div class="inforow">
        <span class="label">Durée du test</span>
        <span class="value">? minutes</span>
    </div>

     <div class="inforow">
        <span class="label">Questions</span>
        <span class="value">? questions</span>
     </div>

      <div class="inforow">
        <span class="label">Type</span>
        <span class="value">QCM?</span>
      </div>
        
        
    </div>
 

</main>

    <footer>

    </footer>


<?php
elseif($page === "test"):
?>

     <header>
        <div class="entete-text">
            <h1>Test d'admission</h1>
        </div>

        <div class="timer" role="timer" aria-live="polite" aria-label="Temps restant">
        60s
        </div>
    </header>

    <main>
        <div class="progress-box">

            <div class="loading-container">
        
            <div class="progress-header">
                <span class="progress-title">Progression totale</span>
             <span class="progress-percent"><span id="count">15</span>%</span>
            </div>
                <div id="barre" role="progressbar" aria-label="Progression du test" aria-valuemin="0" aria-valuemax="100" aria-valuenow="15">
                    <div id="progres"></div>
                </div>

             </div>

        </div>

        <div class="container">

        <div class="contenu">
<?php foreach ($questions as $index => $q): ?>
    <div class="question" data-index="<?= $index ?>" style="<?= $index === 0 ? '' : 'display:none;' ?>">
        <h2><?= $q['intitule'] ?></h2>

        <ul>
            <?php foreach ($q['propositions'] as $i => $prop): ?>
                <li>
                    <label>
                        <input type="radio" name="q<?= $index ?>" value="<?= $i ?>">
                        <?= $prop['label'] ?>
                    </label>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endforeach; ?>
</div>


       </div>


        <div class="under-container">

             <div class="question-nav">
                <button class="btn-nav"> < précédent </button>
                <button class="btn-nav"> suivant > </button>
            </div>

            <div class="quiz-nav">
                <div class="box-container" id="box-container"></div>
            </div>

        </div>

       <div class="btn-box">
    <button type="button" class="submit" id="submit" disabled>Soumettre le test</button>


      
    </main>

      <script>
    const BASE_URL = "<?= BASE_URL ?>";
    <?php
$page = $_GET["page"] ?? "dashboard";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
      <?php if ($page === "dashboard"): ?>
        <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/accesAuTest.css">
    <?php elseif ($page === "test"): ?>
        <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/progression.css">
    <?php else: ?>
        <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/testTermine.css">
    <?php endif; ?>
</head>
<body>

<?php
if($page === "dashboard"):
?>
    <header>

    <div class="partgauche">
    <div class="logo"><img src="<?= BASE_URL ?>/public/assets/images/CWL.png" alt=""></div>
        <div class="textheader">
    <h1>Plateforme admission</h1>
    <h2>Espace candidat</h2>
        </div>
    </div>


    <div class="partdroite">
        <div class="infouser">
    <span>Connecté an tant que </span>
    <strong>Nom (on recupera en php plutard)</strong>
        </div>
     <button>
       <div class="icone"><img src="<?= BASE_URL ?>/public/assets/images/icone deconnexion.png" alt=""></div> Déconnexion
    </button>
    </div>

    </header>

 <main>

    <div class="titre">

    <div class="namespace">
        Dashboard candidat
    </div>
    <div class="phraseintro">
        Bienvenue, consultez votre test d'admission et suivez votre progression.
    </div>

    </div>


    <div class="card">

    <div class="entete">
        
        <div class="logo"><img src="<?= BASE_URL ?>/public/assets/images/iconeFeuille.png" alt=""></div>
    <div class="textcard">
        <h3>Accéder au test</h3>
        <h4>Test d'admission 2026</h4>
    </div>

     </div>


    <div class="autorisation">

    <div class="coche"><div class="icone"><img src="<?= BASE_URL ?>/public/assets/images/iconeCoche.png" alt=""></div> </div>

        <div class="textauto">
        <h3>Accès autorisé</h3>
        <h4>Vous pouvez commencer votre test d'admission.</h4>
        </div>
    </div>

        <a href="<?= BASE_URL ?>/candidat?page=test">
    <button class="btnStart">Passer le test 
        <span class="arrow">→</span>
    </button>
        </a>
</div>


    <div class="informations">

    <h3>Informations</h3>

    <div class="inforow">
        <span class="label">Durée du test</span>
        <span class="value">? minutes</span>
    </div>

     <div class="inforow">
        <span class="label">Questions</span>
        <span class="value">? questions</span>
     </div>

      <div class="inforow">
        <span class="label">Type</span>
        <span class="value">QCM?</span>
      </div>
        
        
    </div>
 

</main>

    <footer>

    </footer>


<?php
elseif($page === "test"):
?>

     <header>
        <div class="entete-text">
            <h1>Test d'admission</h1>
        </div>

        <div class="timer">
        60s
        </div>
    </header>

    <main>
        <div class="progress-box">

            <div class="loading-container">
        
            <div class="progress-header">
                <span class="progress-title">Progression totale</span>
             <span class="progress-percent"><span id="count">15</span>%</span>
            </div>
                <div id="barre">
                    <div id="progres"></div>
                </div>

             </div>

        </div>

        <div class="container">

        <div class="contenu">
<?php foreach ($questions as $index => $q): ?>
    <div class="question" data-index="<?= $index ?>" style="<?= $index === 0 ? '' : 'display:none;' ?>">
        <h2><?= $q['intitule'] ?></h2>

        <ul>
            <?php foreach ($q['propositions'] as $i => $prop): ?>
                <li>
                    <label>
                        <input type="radio" name="q<?= $index ?>" value="<?= $i ?>">
                        <?= $prop['label'] ?>
                    </label>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endforeach; ?>
</div>


       </div>


        <div class="under-container">

             <div class="question-nav">
                <button class="btn-nav"> précédent </button>
                <button class="btn-nav"> suivant > </button>
            </div>

            <div class="quiz-nav">
                <div class="box-container" id="box-container"></div>
            </div>

        </div>

       <div class="btn-box">
    <button type="button" class="submit" id="submit" disabled>Soumettre le test</button>
        </div>

      
    </main>

      <script>
    const BASE_URL = "<?= BASE_URL ?>";
        </script>

     <!-- script externe désactivé pour debug -->
        
    <footer>

    </footer>

    <?php
elseif($page === "finish"):
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
                <div class="value">10 mai 2026</div>
            </div>

            <div>
                <div class="label">Heure de soumission</div>
                <div class="value">14:35</div>
            </div>

            <div>
                <div class="label">Numéro de référence</div>
                <div class="value"> (a recup en php )TEST-2026-5847</div>
            </div>

            <div>
                <div class="label">Statut</div>
                <div class="value" style="color: #16A34A;">Validé</div>
            </div>
        </div>

        <div class="btn-box">
        <button class="btnStart"> <div class="logo"><img src="<?= BASE_URL ?>/public/assets/images/deconnexion1.png" alt=""></div> Quitter la page</button>
        </div>

        <div class="footer-text">
            Vous pouvez fermer cette fenêtre ou retourner à votre tableau de bord
            <br><br>
            Des questions ? Contactez-nous à 
            ...</a>
        </div>

    </div>

</div>

<?php endif ?>

<script>
    const BASE_URL = "<?= BASE_URL ?>";
</script>
<script src="<?= BASE_URL ?>/public/assets/js/test.js"></script>




</body>

</html>

    <footer>

    </footer>

    <?php
elseif($page === "finish"):
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
                <div class="value">10 mai 2026</div>
            </div>

            <div>
                <div class="label">Heure de soumission</div>
                <div class="value">14:35</div>
            </div>

            <div>
                <div class="label">Numéro de référence</div>
                <div class="value"> (a recup en php )TEST-2026-5847</div>
            </div>

            <div>
                <div class="label">Statut</div>
                <div class="value" style="color: #16A34A;">Validé</div>
            </div>
        </div>

        <div class="btn-box">
        <button class="btnStart"> <div class="logo"><img src="<?= BASE_URL ?>/public/assets/images/deconnexion1.png" alt=""></div> Quitter la page</button>
        </div>

        <div class="footer-text">
            Vous pouvez fermer cette fenêtre ou retourner à votre tableau de bord
            <br><br>
            Des questions ? Contactez-nous à 
            ...</a>
        </div>

    </div>

</div>

<?php endif ?>
<script>
    const testData = {
        questions: <?= json_encode($questions, JSON_UNESCAPED_UNICODE) ?>
    };
</script>

<!-- PANNEAU PROFIL (slide depuis la droite) -->
<div class="modal-overlay" id="modal-profil" style="display:none" role="dialog" aria-modal="true" aria-labelledby="modal-titre">
    <div class="modal-card">

        <!-- En-tête avec avatar initiales + nom -->
        <div class="modal-header">
            <div class="modal-avatar" aria-hidden="true">
                <?= strtoupper(mb_substr($user['prenom'] ?? '?', 0, 1)) ?>
            </div>
            <div class="modal-header-info">
                <span class="modal-header-name"><?= htmlspecialchars(trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? ''))) ?></span>
                <span class="modal-header-sub"><?= htmlspecialchars($user['email'] ?? '') ?></span>
            </div>
        </div>

        <!-- Corps : formulaire -->
        <div class="modal-body">
            <h2 id="modal-titre">Modifier le profil</h2>

            <div id="msg-profil" class="msg-profil" style="display:none"></div>

            <form id="form-profil" novalidate>
                <div class="modal-group">
                    <label for="m-prenom">Prénom</label>
                    <input type="text" id="m-prenom" name="prenom"
                           value="<?= htmlspecialchars($user['prenom'] ?? '') ?>">
                    <span class="modal-error" id="err-prenom"></span>
                </div>

                <div class="modal-group">
                    <label for="m-nom">Nom</label>
                    <input type="text" id="m-nom" name="nom"
                           value="<?= htmlspecialchars($user['nom'] ?? '') ?>">
                    <span class="modal-error" id="err-nom"></span>
                </div>

                <div class="modal-group">
                    <label for="m-email">E-mail</label>
                    <input type="email" id="m-email" name="email"
                           value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                    <span class="modal-error" id="err-email"></span>
                </div>
            </form>
        </div>

        <!-- Pied : actions -->
        <div class="modal-footer">
            <button type="submit" form="form-profil" class="modal-btn-save">Enregistrer</button>
            <button type="button" class="modal-btn-retour" id="btn-fermer-profil">Retour</button>
        </div>

    </div>
</div>

<script>
(function () {
    const overlay  = document.getElementById('modal-profil');
    const btnOuvrir = document.getElementById('btn-ouvrir-profil');
    const btnFermer = document.getElementById('btn-fermer-profil');
    const form     = document.getElementById('form-profil');
    const msg      = document.getElementById('msg-profil');

    if (!overlay || !btnOuvrir) return;

    function ouvrir() {
        overlay.style.position = 'fixed';
        overlay.style.top      = '0';
        overlay.style.left     = '0';
        overlay.style.right    = '0';
        overlay.style.bottom   = '0';
        overlay.style.zIndex   = '9999';
        overlay.style.display  = 'flex';
        document.body.style.overflow = 'hidden';
        btnFermer.focus();
    }

    function fermer() {
        overlay.style.display = 'none';
        document.body.style.overflow = '';
        btnOuvrir.focus();
        msg.style.display = 'none';
        msg.textContent = '';
        ['prenom','nom','email'].forEach(function(f) {
            var el = document.getElementById('err-' + f);
            if (el) el.textContent = '';
        });
    }

    btnOuvrir.addEventListener('click', ouvrir);
    btnFermer.addEventListener('click', fermer);

    // Fermeture Échap
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && overlay.style.display !== 'none') fermer();
    });

    // Fermeture clic sur le fond
    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) fermer();
    });

    // Soumission AJAX
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        // Efface erreurs précédentes
        ['prenom','nom','email'].forEach(function(f) {
            document.getElementById('err-' + f).textContent = '';
        });
        msg.style.display = 'none';

        var data = new FormData(form);

        fetch('<?= BASE_URL ?>/profile', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: data
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.success) {
                var strong = document.querySelector('.infouser strong');
                if (strong) strong.textContent = res.nom_complet;

                msg.textContent    = 'Profil mis à jour.';
                msg.className      = 'msg-profil msg-profil--ok';
                msg.style.display  = 'block';
            } else {
                Object.keys(res.errors).forEach(function(f) {
                    var el = document.getElementById('err-' + f);
                    if (el) el.textContent = res.errors[f];
                });
            }
        })
        .catch(function() {
            msg.textContent    = 'Erreur réseau, veuillez réessayer.';
            msg.className      = 'msg-profil msg-profil--err';
            msg.style.display  = 'block';
        });
    });
})();
</script>

</body>

</html>