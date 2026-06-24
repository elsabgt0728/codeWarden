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
        <link rel="stylesheet" href="accesAuTest.css">
    <?php elseif ($page === "test"): ?>
        <link rel="stylesheet" href="progression.css">
    <?php else: ?>
        <link rel="stylesheet" href="testTermine.css">
    <?php endif; ?>
</head>
<body>

<?php
if($page === "dashboard"):
?>
    <header>

    <div class="partgauche">
    <div class="logo"><img src="CWL.png" alt=""></div>
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
       <div class="icone"><img src="icone deconnexion.png" alt=""></div> Déconnexion
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
        
        <div class="logo"><img src="icone feuille.png" alt=""></div>
    <div class="textcard">
        <h3>Accéder au test</h3>
        <h4>Test d'admission 2026</h4>
    </div>

     </div>


    <div class="autorisation">

    <div class="coche"><div class="icone"><img src="icone coche.png" alt=""></div> </div>

        <div class="textauto">
        <h3>Accès autorisé</h3>
        <h4>Vous pouvez commencer votre test d'admission.</h4>
        </div>
    </div>

        <a href="pageCandidat.php?page=test">
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
            <a href="pageCandidat.php?page=finish">
            <button type="submit" class="submit" id="submit" disabled>Soumettre le test</button>
            </a>
        </div>

    </main>

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
                <div class="logo"><img src="check.png" alt=""></div>
                <div class="info-text">
                    <h3>Votre test est enregistré</h3>
                    <p>Toutes vos réponses ont été sauvegardées et sont en cours de traitement.</p>
                </div>
            </div>

            <div class="info-row">
                <div class="logo"><img src="enveloppe.png" alt=""></div>
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
        <button class="btnStart"> <div class="logo"><img src="deconnexion1.png" alt=""></div> Quitter la page</button>
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

</body>
</html>