<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeWarden – Administration</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/CodeWardenAdminCSS.css?v=3">
</head>
<body>

<header>
    <nav class="header-inner" aria-label="Navigation principale">
        <div class="nav-group">
            <a class="navbar-link <?= in_array($page, ['exercices','liste_exercice','creer_exercice']) ? 'active' : '' ?>"
               href="<?= BASE_URL ?>/admin?page=exercices"
               <?= in_array($page, ['exercices','liste_exercice','creer_exercice']) ? 'aria-current="page"' : '' ?>>
                Exercices
            </a>
            <a class="navbar-link <?= in_array($page, ['tests','create_test','epreuve']) ? 'active' : '' ?>"
               href="<?= BASE_URL ?>/admin?page=tests"
               <?= in_array($page, ['tests','create_test','epreuve']) ? 'aria-current="page"' : '' ?>>
                Tests
            </a>
        </div>
        <div class="header-logo">CodeWarden</div>
        <div class="nav-group">
            <a class="navbar-link <?= in_array($page, ['etudiants','liste_candidat','attribution_test']) ? 'active' : '' ?>"
               href="<?= BASE_URL ?>/admin?page=etudiants"
               <?= in_array($page, ['etudiants','liste_candidat','attribution_test']) ? 'aria-current="page"' : '' ?>>
                Étudiants
            </a>
            <a class="navbar-link <?= $page === 'statistiques' ? 'active' : '' ?>"
               href="<?= BASE_URL ?>/admin?page=statistiques"
               <?= $page === 'statistiques' ? 'aria-current="page"' : '' ?>>
                Statistiques
            </a>
            <a class="navbar-link" href="<?= BASE_URL ?>/admin/logout">Déconnexion</a>
        </div>
    </nav>
</header>

<div class="layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <?php if (in_array($page, ['exercices', 'liste_exercice', 'creer_exercice'])): ?>
            <h2>Mes exercices</h2>
            <ul>
                <li><a href="<?= BASE_URL ?>/admin?page=liste_exercice"
                       class="sidebar-link <?= $page === 'liste_exercice' ? 'active' : '' ?>">
                    Liste des exercices
                </a></li>
                <li><a href="<?= BASE_URL ?>/admin?page=creer_exercice"
                       class="sidebar-link <?= $page === 'creer_exercice' ? 'active' : '' ?>">
                    Créer un exercice
                </a></li>
            </ul>

        <?php elseif (in_array($page, ['tests', 'create_test', 'epreuve'])): ?>
            <h2>Mes tests</h2>
            <ul>
                <li><a href="<?= BASE_URL ?>/admin?page=create_test"
                       class="sidebar-link <?= $page === 'create_test' ? 'active' : '' ?>">
                    + Créer un test
                </a></li>
                <?php foreach ($tests as $test): ?>
                <li><a href="<?= BASE_URL ?>/admin?page=epreuve&id=<?= $test['id_test'] ?>"
                       class="sidebar-link <?= ($page === 'epreuve' && (string)($id ?? '') === (string)$test['id_test']) ? 'active' : '' ?>">
                    <?= htmlspecialchars($test['titre']) ?>
                </a></li>
                <?php endforeach; ?>
                <?php if (empty($tests)): ?>
                <li><span class="sidebar-empty">Aucun test</span></li>
                <?php endif; ?>
            </ul>

        <?php elseif (in_array($page, ['etudiants', 'liste_candidat', 'attribution_test'])): ?>
            <h2>Étudiants</h2>
            <ul>
                <li><a href="<?= BASE_URL ?>/admin?page=liste_candidat"
                       class="sidebar-link <?= $page === 'liste_candidat' ? 'active' : '' ?>">
                    Liste des candidats
                </a></li>
                <li><a href="<?= BASE_URL ?>/admin?page=attribution_test"
                       class="sidebar-link <?= $page === 'attribution_test' ? 'active' : '' ?>">
                    Attribution du test
                </a></li>
            </ul>

        <?php elseif ($page === 'statistiques'): ?>
            <h2>Statistiques</h2>

        <?php else: ?>
            <h2>Navigation</h2>
        <?php endif; ?>

    </aside>

    <!-- CONTENU CENTRAL -->
    <main class="content-center">

        <?php if (!empty($success)): ?>
            <div class="flash-success" role="alert"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <!-- ── PAGE EXERCICES (accueil section) ── -->
        <?php if ($page === 'exercices'): ?>
            <div class="page-hero">
                <a href="<?= BASE_URL ?>/admin?page=creer_exercice" class="create-exo-btn">
                    Créer un exercice
                </a>
                <a href="<?= BASE_URL ?>/admin?page=liste_exercice" class="create-exo-btn btn-outline">
                    Voir les exercices
                </a>
            </div>

        <!-- ── PAGE CRÉER UN EXERCICE ── -->
        <?php elseif ($page === 'creer_exercice'): ?>
            <div class="page-wrap">
                <h2>Créer un exercice</h2>

                <?php if (!empty($errors_jeu)): ?>
                    <div class="alert alert-error" role="alert">Veuillez corriger les erreurs ci-dessous.</div>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>/admin/jeux/creer" method="POST" enctype="multipart/form-data" novalidate>

                    <div class="form-group">
                        <label for="titre">Titre</label>
                        <input type="text" name="titre" id="titre" required
                               value="<?= htmlspecialchars($old_jeu['titre'] ?? '') ?>">
                        <?php if (!empty($errors_jeu['titre'])): ?>
                            <span class="error-msg"><?= htmlspecialchars($errors_jeu['titre']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="description">Description / Consigne</label>
                        <textarea name="description" id="description" rows="4"><?= htmlspecialchars($old_jeu['description'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="bareme">Barème (points)</label>
                        <input type="number" name="bareme" id="bareme" min="1" required
                               value="<?= htmlspecialchars($old_jeu['bareme'] ?? '') ?>">
                        <?php if (!empty($errors_jeu['bareme'])): ?>
                            <span class="error-msg"><?= htmlspecialchars($errors_jeu['bareme']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="type">Type</label>
                        <select name="type" id="type">
                            <?php
                            $types = ['qcm' => 'QCM', 'texte_libre' => 'Texte libre', 'glisser_deposer' => 'Glisser-déposer', 'association' => 'Association', 'autre' => 'Autre'];
                            foreach ($types as $val => $label):
                                $sel = ($old_jeu['type'] ?? '') === $val ? 'selected' : '';
                            ?>
                            <option value="<?= $val ?>" <?= $sel ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!empty($errors_jeu['type'])): ?>
                            <span class="error-msg"><?= htmlspecialchars($errors_jeu['type']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="difficulte">Difficulté</label>
                        <select name="difficulte" id="difficulte">
                            <?php
                            $niveaux = ['facile' => 'Facile', 'moyen' => 'Moyen', 'difficile' => 'Difficile', 'expert' => 'Expert'];
                            foreach ($niveaux as $val => $label):
                                $sel = ($old_jeu['difficulte'] ?? 'moyen') === $val ? 'selected' : '';
                            ?>
                            <option value="<?= $val ?>" <?= $sel ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="statut">Statut</label>
                        <select name="statut" id="statut">
                            <?php
                            $statuts = ['actif' => 'Actif', 'inactif' => 'Inactif', 'brouillon' => 'Brouillon'];
                            foreach ($statuts as $val => $label):
                                $sel = ($old_jeu['statut'] ?? 'brouillon') === $val ? 'selected' : '';
                            ?>
                            <option value="<?= $val ?>" <?= $sel ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="nbquestions">Nombre de questions</label>
                        <input type="number" name="nbquestions" id="nbquestions" min="0" value="0">
                    </div>

                    <div id="questions-container"></div>

                    <button type="submit" class="btn-submit">Créer l'exercice</button>

                </form>
            </div>

        <!-- ── PAGE LISTE DES EXERCICES ── -->
        <?php elseif ($page === 'liste_exercice'): ?>
            <div class="table-wrap">
                <div class="table-wrap-header">
                    <h3>Liste des exercices</h3>
                    <a href="<?= BASE_URL ?>/admin?page=creer_exercice" class="btn-submit btn-sm">+ Nouveau</a>
                </div>
                <div class="table-container">
                    <?php if (empty($liste_jeux)): ?>
                        <p class="table-empty">Aucun exercice créé pour l'instant.</p>
                    <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Titre</th>
                                <th scope="col">Type</th>
                                <th scope="col">Difficulté</th>
                                <th scope="col">Barème</th>
                                <th scope="col">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($liste_jeux as $jeu): ?>
                            <tr>
                                <td><?= htmlspecialchars($jeu['id_jeux']) ?></td>
                                <td><?= htmlspecialchars($jeu['titre']) ?></td>
                                <td><?= htmlspecialchars($jeu['type']) ?></td>
                                <td><?= htmlspecialchars($jeu['difficulte']) ?></td>
                                <td><?= htmlspecialchars($jeu['bareme']) ?></td>
                                <td>
                                    <?php
                                    $badgeClass = match($jeu['statut']) {
                                        'actif'    => 'badge-green',
                                        'inactif'  => 'badge-red',
                                        default    => 'badge-orange',
                                    };
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($jeu['statut']) ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>

        <!-- ── PAGE TESTS (accueil section) ── -->
        <?php elseif ($page === 'tests'): ?>
            <div class="page-hero">
                <a href="<?= BASE_URL ?>/admin?page=create_test" class="create-exo-btn">
                    Créer un test
                </a>
            </div>

        <!-- ── PAGE CRÉER UN TEST ── -->
        <?php elseif ($page === 'create_test'): ?>
            <div class="page-wrap">
                <h2>Créer un test</h2>

                <?php if (!empty($errors_test)): ?>
                    <div class="alert alert-error" role="alert">Veuillez corriger les erreurs ci-dessous.</div>
                <?php endif; ?>

                <form id="creation_test" action="<?= BASE_URL ?>/admin/tests/creer" method="POST" novalidate>

                    <div class="form-group">
                        <label for="titre">Titre de l'épreuve</label>
                        <input type="text" name="titre" id="titre" required
                               value="<?= htmlspecialchars($old_test['titre'] ?? '') ?>">
                        <?php if (!empty($errors_test['titre'])): ?>
                            <span class="error-msg"><?= htmlspecialchars($errors_test['titre']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="duree">Durée (en minutes)</label>
                        <input type="number" name="duree" id="duree" min="1" required
                               value="<?= htmlspecialchars($old_test['duree'] ?? '') ?>">
                        <?php if (!empty($errors_test['duree'])): ?>
                            <span class="error-msg"><?= htmlspecialchars($errors_test['duree']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <p class="form-label">Exercices inclus</p>
                        <?php if (empty($liste_jeux)): ?>
                            <p class="hint-text">Aucun exercice disponible.
                                <a href="<?= BASE_URL ?>/admin?page=creer_exercice">Créer un exercice d'abord.</a>
                            </p>
                        <?php else: ?>
                        <div class="checkbox-list">
                            <?php foreach ($liste_jeux as $jeu): ?>
                            <label class="checkbox-item">
                                <input type="checkbox" name="jeux[]" value="<?= $jeu['id_jeux'] ?>"
                                    <?= in_array($jeu['id_jeux'], $old_test['jeux'] ?? []) ? 'checked' : '' ?>>
                                <?= htmlspecialchars($jeu['titre']) ?>
                                <span class="hint">(<?= htmlspecialchars($jeu['type']) ?>, <?= htmlspecialchars($jeu['bareme']) ?> pts)</span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($errors_test['jeux'])): ?>
                            <span class="error-msg"><?= htmlspecialchars($errors_test['jeux']) ?></span>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn-submit">Créer le test</button>

                </form>
            </div>

        <!-- ── PAGE DÉTAIL D'UNE ÉPREUVE ── -->
        <?php elseif ($page === 'epreuve'): ?>
            <div class="table-wrap">
                <?php if (!$epreuve): ?>
                    <p class="table-empty">Épreuve introuvable.</p>
                <?php else: ?>
                    <div class="table-wrap-header">
                        <h3><?= htmlspecialchars($epreuve['titre']) ?></h3>
                        <span class="badge <?= $epreuve['statut'] === 'actif' ? 'badge-green' : 'badge-orange' ?>">
                            <?= htmlspecialchars($epreuve['statut']) ?>
                        </span>
                    </div>
                    <div class="epreuve-meta">
                        <span><strong>Durée :</strong> <?= (int)($epreuve['duree_minutes'] ?? $epreuve['duree'] ?? 0) ?> min</span>
                        <?php if (!empty($epreuve['description'])): ?>
                        <p><strong>Description :</strong> <?= htmlspecialchars($epreuve['description']) ?></p>
                        <?php endif; ?>
                    </div>
                    <h4 class="section-title">Exercices inclus</h4>
                    <div class="table-container">
                        <?php if (empty($jeux_epreuve)): ?>
                            <p class="table-empty">Aucun exercice associé à ce test.</p>
                        <?php else: ?>
                        <table>
                            <thead>
                                <tr>
                                    <th scope="col">Titre</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Difficulté</th>
                                    <th scope="col">Barème</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($jeux_epreuve as $jeu): ?>
                                <tr>
                                    <td><?= htmlspecialchars($jeu['titre']) ?></td>
                                    <td><?= htmlspecialchars($jeu['type']) ?></td>
                                    <td><?= htmlspecialchars($jeu['difficulte']) ?></td>
                                    <td><?= htmlspecialchars($jeu['bareme']) ?> pts</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

        <!-- ── PAGE ÉTUDIANTS (accueil section) ── -->
        <?php elseif ($page === 'etudiants'): ?>
            <div class="page-hero">
                <a href="<?= BASE_URL ?>/admin?page=liste_candidat" class="create-exo-btn">
                    Voir les candidats
                </a>
                <a href="<?= BASE_URL ?>/admin?page=attribution_test" class="create-exo-btn btn-outline">
                    Attribuer un test
                </a>
            </div>

        <!-- ── PAGE LISTE DES CANDIDATS ── -->
        <?php elseif ($page === 'liste_candidat'): ?>
            <div class="table-wrap">
                <div class="table-wrap-header">
                    <h3>Liste des candidats</h3>
                </div>
                <div class="table-container">
                    <?php if (empty($candidats)): ?>
                        <p class="table-empty">Aucun candidat inscrit pour l'instant.</p>
                    <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Nom</th>
                                <th scope="col">Prénom</th>
                                <th scope="col">E-mail</th>
                                <th scope="col">Date de naissance</th>
                                <th scope="col">Code accès</th>
                                <th scope="col">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($candidats as $candidat): ?>
                            <tr>
                                <td><?= htmlspecialchars($candidat['id_candidat']) ?></td>
                                <td><?= htmlspecialchars($candidat['nom']) ?></td>
                                <td><?= htmlspecialchars($candidat['prenom']) ?></td>
                                <td><?= htmlspecialchars($candidat['email']) ?></td>
                                <td><?= htmlspecialchars($candidat['date_naissance'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($candidat['code_acces'] ?? '—') ?></td>
                                <td>
                                    <?php
                                    $badgeClass = match($candidat['statut']) {
                                        'actif'     => 'badge-green',
                                        'suspendu'  => 'badge-red',
                                        default     => 'badge-orange',
                                    };
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($candidat['statut']) ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>

        <!-- ── PAGE ATTRIBUTION DU TEST ── -->
        <?php elseif ($page === 'attribution_test'): ?>
            <div class="page-wrap">
                <h2>Attribution d'une épreuve</h2>

                <?php if (!empty($errors_attribution)): ?>
                    <div class="alert alert-error" role="alert">Veuillez corriger les erreurs ci-dessous.</div>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>/admin/attribution" method="POST" novalidate>

                    <div class="form-group">
                        <label for="candidat">Candidat</label>
                        <select name="candidat" id="candidat" required>
                            <option value="">-- Sélectionner un candidat --</option>
                            <?php foreach ($candidats as $candidat): ?>
                            <option value="<?= $candidat['id_candidat'] ?>"
                                <?= ($old_attribution['candidat'] ?? '') == $candidat['id_candidat'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($candidat['nom'] . ' ' . $candidat['prenom']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!empty($errors_attribution['candidat'])): ?>
                            <span class="error-msg"><?= htmlspecialchars($errors_attribution['candidat']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="test">Épreuve</label>
                        <select name="test" id="test" required>
                            <option value="">-- Sélectionner une épreuve --</option>
                            <?php foreach ($tests as $test): ?>
                            <option value="<?= $test['id_test'] ?>"
                                <?= ($old_attribution['test'] ?? '') == $test['id_test'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($test['titre']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!empty($errors_attribution['test'])): ?>
                            <span class="error-msg"><?= htmlspecialchars($errors_attribution['test']) ?></span>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn-submit">Attribuer le test</button>

                </form>
            </div>

        <!-- ── PAGE STATISTIQUES ── -->
        <?php elseif ($page === 'statistiques'): ?>
            <div class="table-wrap">
                <div class="table-wrap-header">
                    <h3>Statistiques globales</h3>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th scope="col">Exercice</th>
                                <th scope="col">Taux de réussite</th>
                                <th scope="col">Tentatives</th>
                                <th scope="col">Moyenne</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($liste_jeux)): ?>
                                <?php foreach ($liste_jeux as $jeu): ?>
                                <tr>
                                    <td><?= htmlspecialchars($jeu['titre']) ?></td>
                                    <td><span class="badge badge-orange">—</span></td>
                                    <td>—</td>
                                    <td>—</td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" style="text-align:center;color:#999;">Aucune donnée disponible.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php endif; ?>

    </main>

</div>

<footer>
    <div class="footer-left">
        <a href="#">About</a>
        <a href="#">Politique</a>
        <a href="#">CGU</a>
    </div>
    <div class="footer-center"><strong>CodeWarden</strong></div>
    <div class="footer-right">Administration</div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var nbInput = document.getElementById('nbquestions');
    if (!nbInput) return;

    nbInput.addEventListener('change', function () {
        var nb        = Math.max(0, parseInt(this.value) || 0);
        var container = document.getElementById('questions-container');
        container.innerHTML = '';
        for (var i = 1; i <= nb; i++) {
            container.innerHTML += '<div class="question-block">'
                + '<h3>Question ' + i + '</h3>'
                + '<div class="form-group"><label for="question_intitule_' + i + '">Intitulé</label>'
                + '<textarea id="question_intitule_' + i + '" name="question_intitule_' + i + '" rows="2" required></textarea></div>'
                + '<div class="form-group"><label for="question_points_' + i + '">Points</label>'
                + '<input type="number" id="question_points_' + i + '" name="question_points_' + i + '" min="1" value="1" required></div>'
                + '<div class="form-group"><label for="question_type_' + i + '">Type</label>'
                + '<select id="question_type_' + i + '" name="question_type_' + i + '">'
                + '<option value="texte">Réponse texte</option>'
                + '<option value="qcm">QCM</option>'
                + '</select></div>'
                + '<div class="form-group"><label for="question_bonne_' + i + '">Bonne réponse (si QCM)</label>'
                + '<input type="text" id="question_bonne_' + i + '" name="question_bonne_' + i + '"></div>'
                + '</div>';
        }
    });
});
</script>

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