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

        <div class="timer" id ="timer" role="timer" aria-live="polite" aria-label="Temps restant">
        <?= $duree ?>:00
        </div>

        <script>
            const TEST_DURATION = <?= $duree ?> * 60; // en secondes
        </script>
    </header>

    <main>
        <div class="progress-box">

            <div class="loading-container">
        
            <div class="progress-header">
                <span class="progress-title">Progression totale</span>
             <span class="progress-percent"><span id="count">0</span>%</span>
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
</script>
<script src="<?= BASE_URL ?>/public/assets/js/test.js"></script>


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

<script src="<?= BASE_URL ?>/public/assets/js/candidat.js">
</script>

</body>

</html>