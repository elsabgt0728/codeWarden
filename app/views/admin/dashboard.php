<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>CodeWarden – Administration</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/CodeWardenAdminCSS.css">
</head>
<body>

<header>
    <div class="header-inner">
        <div class="nav-group">
            <a class="navbar-link <?= $page === 'exercices'    ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin?page=exercices">Exercices</a>
            <a class="navbar-link <?= $page === 'tests'        ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin?page=tests">Tests</a>
        </div>
        <div class="header-logo">CodeWarden</div>
        <div class="nav-group">
            <a class="navbar-link <?= $page === 'etudiants'    ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin?page=etudiants">Étudiants</a>
            <a class="navbar-link <?= $page === 'statistiques' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin?page=statistiques">Statistiques</a>
            <a class="navbar-link" href="<?= BASE_URL ?>/admin/logout">Déconnexion</a>
        </div>
    </div>
</header>

<div class="layout">
    <aside class="sidebar">
        <?php if ($page === 'exercices' || $page === 'creer_exercice'): ?>
            <h2>Mes exercices</h2>
            <ul>
                <li><button class="btn-exercice">Exercice 1</button></li>
                <li><button class="btn-exercice">Exercice 2</button></li>
                <li><button class="btn-exercice">Exercice 3</button></li>
            </ul>
        <?php elseif ($page === 'tests' || $page === 'creer_test'): ?>
            <h2>Mes tests</h2>
            <ul>
                <li><button class="btn-exercice">Test 1</button></li>
                <li><button class="btn-exercice">Test 2</button></li>
            </ul>
        <?php elseif ($page === 'etudiants'): ?>
            <h2>Étudiants</h2>
            <button class="btn-create">Ajouter</button>
            <ul>
                <li><button class="btn-exercice">Groupe A</button></li>
                <li><button class="btn-exercice">Groupe B</button></li>
            </ul>
        <?php elseif ($page === 'statistiques'): ?>
            <h2>Statistiques</h2>
        <?php endif; ?>
    </aside>

    <main class="content-center">
       <?php if ($page === 'exercices' || $page === 'tests'): ?>

        <a class="create-exo-btn" 
           href="<?= BASE_URL ?>/admin?page=<?= $page === 'tests' ? 'creer_test' : 'creer_exercice' ?>">
            Créer un <?= $page === 'tests' ? 'test' : 'exercice' ?>
        </a>
  <?php endif; ?>

 <?php if ($page === 'creer_exercice'): ?>

<div class="form-card">

<form action="<?= BASE_URL ?>/admin?page=creer_exercice_traitement" method="POST" enctype="multipart/form-data">

    <!-- TITRE -->
    <div class="form-group">
        <label for="titre">Titre</label>
        <input type="text" name="titre" id="titre" required>
    </div>

    <!-- DESCRIPTION -->
    <div class="form-group">
        <label for="description">Description / Consigne</label>
        <textarea name="description" id="description" rows="4" required></textarea>
    </div>

    <!-- BAREME -->
    <div class="form-group">
        <label for="bareme">Barème</label>
        <input type="number" name="bareme" id="bareme" min="1" required>
    </div>

    <!-- DIFFICULTÉ -->
    <div class="form-group">
        <label for="difficulte">Difficulté</label>
        <select name="difficulte" id="difficulte" required>
            <?php foreach ($difficultes as $d): ?>
                <option value="<?= htmlspecialchars($d) ?>">
                    <?= ucfirst($d) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- TYPE -->
    <div class="form-group">
        <label for="categorie">Catégorie</label>
        <select name="categorie" id="categorie" required>
            <?php foreach ($types as $t): ?>
                <option value="<?= htmlspecialchars($t) ?>">
                    <?= ucfirst(str_replace('_', ' ', $t)) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- DURÉE -->
    <div class="form-group">
        <label for="duree">Durée estimée (secondes)</label>
        <input type="number" name="duree" id="duree" min="1">
    </div>

    <!-- STATUT -->
    <div class="form-group">
        <label for="statut">Statut</label>
        <select name="statut" id="statut">
            <option value="actif">Actif</option>
            <option value="inactif">Inactif</option>
            <option value="brouillon">Brouillon</option>
        </select>
    </div>

    <!-- NOMBRE DE QUESTIONS -->
    <div class="form-group">
        <label for="nbquestions">Nombre de questions</label>
        <input type="number" name="nbquestions" id="nbquestions" min="1">
    </div>

    <!-- CONTENEUR QUESTIONS -->
    <div id="questions-container"></div>

    <!-- BOUTON -->
    <button type="submit" class="btn-submit">Créer l'exercice</button>

</form>

<h3>Aperçu JSON généré</h3>
<pre id="json-preview" style="background:#111;color:#0f0;padding:20px;border-radius:10px;"></pre>

</div>


<?php elseif ( $page === 'creer_test'): ?>

 <div class="form-card">

        <h2>Créer un test</h2>

        <form action="<?= BASE_URL ?>/app/controllers/test_controller.php?action=creer" method="POST">

            <div class="form-group">
                <label for="titre_test">Titre du test</label>
                <input type="text" name="titre_test" id="titre_test" required>
            </div>

            <div class="form-group">
                <label for="duree">Durée (minutes)</label>
                <input type="number" name="duree" id="duree" min="1" value="60" required>
            </div>

            <div class="form-group">
                <label>Jeux à inclure</label>
                <?php foreach ($jeux as $jeu): ?>
                    <div class="checkbox-line">
                        <label>
                            <input type="checkbox" name="jeux[]" value="<?= $jeu['id_jeux'] ?>">
                            <?= htmlspecialchars($jeu['titre']) ?>
                            (<?= $jeu['type'] ?> - <?= $jeu['difficulte'] ?>)
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="form-group">
                <label>Candidats concernés</label>
                <?php foreach ($candidats as $c): ?>
                    <div class="checkbox-line">
                        <label>
                            <input type="checkbox" name="candidats[]" value="<?= $c['id_candidat'] ?>">
                            <?= htmlspecialchars($c['nom']) ?> <?= htmlspecialchars($c['prenom']) ?>
                            (<?= htmlspecialchars($c['email']) ?>)
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="submit" class="btn-submit">Créer le test</button>

        </form>

    </div>
 <?php endif; ?>

    <?php if ($page === 'etudiants'): ?>

            <div class="table-wrap">
                <h3>Liste des étudiants</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr><th>Nom</th><th>Prénom</th><th>Groupe</th><th>Exercices</th><th>Moyenne</th><th>Statut</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>Dupont</td><td>Alice</td><td>Groupe A</td><td>12</td><td>15/20</td><td><span class="badge badge-green">Actif</span></td></tr>
                            <tr><td>Martin</td><td>Bob</td><td>Groupe B</td><td>8</td><td>11/20</td><td><span class="badge badge-green">Actif</span></td></tr>
                            <tr><td>Bernard</td><td>Clara</td><td>Groupe A</td><td>5</td><td>9/20</td><td><span class="badge badge-red">Inactif</span></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($page === 'statistiques'): ?>
            <div class="table-wrap">
                <h3>Statistiques globales</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr><th>Exercice</th><th>Réussite</th><th>Tentatives</th><th>Moyenne</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>Exercice 1</td><td><span class="badge badge-green">87%</span></td><td>45</td><td>14.2/20</td></tr>
                            <tr><td>Exercice 2</td><td><span class="badge badge-orange">63%</span></td><td>38</td><td>11.5/20</td></tr>
                            <tr><td>Exercice 3</td><td><span class="badge badge-red">41%</span></td><td>52</td><td>9.1/20</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </main>
</div>

<footer>
    <div class="footer-center"><strong>CodeWarden</strong></div>
</footer>

</body>

<script>
document.addEventListener("DOMContentLoaded", () => {

    const nbQuestionsInput = document.getElementById("nbquestions");
    const container = document.getElementById("questions-container");

    nbQuestionsInput.addEventListener("input", () => {
        const nb = parseInt(nbQuestionsInput.value);
        container.innerHTML = "";
        if (isNaN(nb) || nb <= 0) return;

        for (let i = 0; i < nb; i++) {
            container.appendChild(creerBlocQuestion(i));
        }
    });

    function creerBlocQuestion(index) {
        const block = document.createElement("div");
        block.classList.add("question-block");

        block.innerHTML = `
            <h3>Question ${index + 1}</h3>

            <div class="form-group">
                <label>Intitulé</label>
                <input type="text" name="questions[${index}][intitule]" required>
            </div>

            <div class="form-group">
                <label>Points</label>
                <input type="number" name="questions[${index}][points]" min="1" required>
            </div>

            <div class="form-group">
                <label>Nombre de propositions</label>
                <input type="number" class="nb-prop" data-q="${index}" min="1" value="4">
            </div>

            <div class="propositions-container" id="props-${index}">
                ${creerPropositions(index, 4)}
            </div>

            <div class="form-group">
                <label>Index de l'intrus</label>
                <input type="number" name="questions[${index}][intrus]" min="0" required>
            </div>
        `;

        // Quand on change le nombre de propositions
        block.querySelector(".nb-prop").addEventListener("input", (e) => {
            const q = e.target.dataset.q;
            const nb = parseInt(e.target.value);
            const propContainer = document.getElementById(`props-${q}`);

            propContainer.innerHTML = creerPropositions(q, nb);
        });

        return block;
    }

    function creerPropositions(qIndex, nb) {
        let html = "";
        for (let p = 0; p < nb; p++) {
            html += `
                <div class="proposition">
                    <input type="text" 
                           name="questions[${qIndex}][propositions][${p}][label]" 
                           placeholder="Texte (optionnel)">

                    <input type="file" 
                           name="questions[${qIndex}][propositions][${p}][image]" 
                           accept="image/*"
                           class="img-input"
                           data-q="${qIndex}" data-p="${p}">

                    <span class="preview" id="preview-${qIndex}-${p}"></span>
                </div>
            `;
        }
        return html;
    }

    // Aperçu image
    document.addEventListener("change", (e) => {
        if (e.target.classList.contains("img-input")) {
            const file = e.target.files[0];
            const q = e.target.dataset.q;
            const p = e.target.dataset.p;

            if (file) {
                const reader = new FileReader();
                reader.onload = () => {
                    document.getElementById(`preview-${q}-${p}`).innerHTML =
                        `<img src="${reader.result}" style="width:80px;border-radius:8px;">`;
                };
                reader.readAsDataURL(file);
            }
        }
    });

});
</script>

</html>