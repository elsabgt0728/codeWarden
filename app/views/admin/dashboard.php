<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeWarden – Administration</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/CodeWardenAdminCSS.css">
</head>
<body>

<header>
    <nav class="header-inner" aria-label="Navigation principale">
        <div class="nav-group">
            <a class="navbar-link <?= $page === 'exercices'    ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin?page=exercices"<?= $page === 'exercices' ? ' aria-current="page"' : '' ?>>Exercices</a>
            <a class="navbar-link <?= $page === 'tests'        ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin?page=tests"<?= $page === 'tests' ? ' aria-current="page"' : '' ?>>Tests</a>
        </div>
        <div class="header-logo">CodeWarden</div>
        <div class="nav-group">
            <a class="navbar-link <?= $page === 'etudiants'    ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin?page=etudiants"<?= $page === 'etudiants' ? ' aria-current="page"' : '' ?>>Étudiants</a>
            <a class="navbar-link <?= $page === 'statistiques' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin?page=statistiques"<?= $page === 'statistiques' ? ' aria-current="page"' : '' ?>>Statistiques</a>
            <a class="navbar-link" href="<?= BASE_URL ?>/admin/logout">Déconnexion</a>
        </div>
    </nav>
</header>

<?php
$toast_msg   = '';
$toast_type  = 'success'; // 'success' | 'error' | 'warning'
$err = $_GET['error']   ?? '';
$ok  = $_GET['success'] ?? '';
if ($ok === 'convoque')       { $toast_msg = 'Convocations envoyées avec succès (' . (int)($_GET['nb'] ?? 0) . ' mail' . ((int)($_GET['nb'] ?? 0) > 1 ? 's' : '') . ').'; }
elseif ($ok === 'modifie')    { $toast_msg = 'Test modifié avec succès.'; }
elseif ($ok === 'supprime')   { $toast_msg = 'Test supprimé.'; }
elseif ($ok === '1')          { $toast_msg = 'Enregistré avec succès.'; }
elseif ($err === 'deja_convoque')  { $toast_msg = 'Ce test a déjà été convoqué. Les mails ont bien été envoyés précédemment.'; $toast_type = 'error'; }
elseif ($err === 'aucun_candidat') { $toast_msg = 'Aucun candidat assigné à ce test. Ajoutez des candidats avant de convoquer.'; $toast_type = 'warning'; }
elseif (isset($_GET['mail_error']))   { $toast_msg = "L'e-mail de décision n'a pas pu être envoyé. Consultez les logs XAMPP pour le détail."; $toast_type = 'error'; }
elseif (isset($_GET['infos_error']))  { $toast_msg = 'Impossible de retrouver le candidat. La décision a quand même été enregistrée.'; $toast_type = 'error'; }
?>
<?php if ($toast_msg): ?>
<div id="toast-notif" class="toast toast-<?= $toast_type ?>">
    <?= htmlspecialchars($toast_msg) ?>
</div>
<script>
    setTimeout(function() {
        var t = document.getElementById('toast-notif');
        if (t) { t.classList.add('toast-hide'); setTimeout(function(){ t.remove(); }, 400); }
    }, 3000);
</script>
<?php endif; ?>


<div class="layout">
    <aside class="sidebar">
        <p class="sidebar-section-label">Exercices</p>
        <?php if (empty($exercices)): ?>
            <p class="sidebar-empty">Aucun exercice.</p>
        <?php else: ?>
        <ul>
            <?php foreach ($exercices as $ex): ?>
            <li>
                <a href="<?= BASE_URL ?>/admin?page=exercices&exercice_id=<?= $ex['id_jeux'] ?>"
                class="btn-exercice<?= isset($exercice_selectionne) && $exercice_selectionne['id_jeux'] === $ex['id_jeux'] ? ' active' : '' ?>">
                    <?= htmlspecialchars($ex['titre']) ?>
                    <span class="badge-statut badge-<?= $ex['statut'] ?>"><?= $ex['statut'] ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>

        <p class="sidebar-section-label" style="margin-top:16px;">Tests</p>
        <?php if (empty($tests)): ?>
            <p class="sidebar-empty">Aucun test.</p>
        <?php else: ?>
        <ul>
            <?php foreach ($tests as $t): ?>
            <li>
                <a href="<?= BASE_URL ?>/admin?page=tests&test_id=<?= $t['id_test'] ?>"
                class="btn-exercice btn-test<?= isset($test_selectionne) && $test_selectionne['id_test'] === $t['id_test'] ? ' active' : '' ?>">
                    <?= htmlspecialchars($t['titre']) ?>
                    <span class="badge-statut badge-<?= $t['statut'] ?>"><?= $t['statut'] ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>

        <?php if ($page === 'statistiques'): ?>
            <p class="sidebar-section-label">Statistiques</p>
        <?php endif; ?>
    </aside>

    <main class="content-center">
       <?php if ($page === 'exercices' || $page === 'tests'): ?>

        <?php if ($page === 'exercices'): ?>
        <?php if ($exercice_selectionne): ?>
            <!-- Détail d'un exercice -->
            <div class="detail-card">
                <div class="detail-header">
                    <h2><?= htmlspecialchars($exercice_selectionne['titre']) ?></h2>
                    <span class="badge badge-statut badge-<?= $exercice_selectionne['statut'] ?>"><?= $exercice_selectionne['statut'] ?></span>
                </div>
                <div class="detail-meta">
                    <span><strong>Type :</strong> <?= htmlspecialchars($exercice_selectionne['type']) ?></span>
                    <span><strong>Difficulté :</strong> <?= htmlspecialchars($exercice_selectionne['difficulte']) ?></span>
                    <span><strong>Barème :</strong> <?= (int)$exercice_selectionne['bareme'] ?> pts</span>
                </div>
                <div class="detail-actions">
                    <a href="<?= BASE_URL ?>/admin/jeux/modifier?id=<?= $exercice_selectionne['id_jeux'] ?>" class="btn-action btn-edit">Modifier</a>
                    <form method="POST" action="<?= BASE_URL ?>/admin/jeux/supprimer" onsubmit="return confirm('Supprimer cet exercice ?')" style="display:inline">
                        <input type="hidden" name="id_jeux" value="<?= $exercice_selectionne['id_jeux'] ?>">
                        <button type="submit" class="btn-action btn-delete">Supprimer</button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <!-- Liste de tous les exercices -->
            <a class="create-exo-btn" href="<?= BASE_URL ?>/admin?page=creer_exercice">+ Créer un exercice</a>
            <div class="table-wrap" style="margin-top:24px;">
            <?php if (empty($exercices)): ?>
                <p style="color:#9ca3af;text-align:center;padding:24px;">Aucun exercice.</p>
            <?php else: ?>
            <div class="table-container"><table>
                <thead><tr><th>Titre</th><th>Type</th><th>Difficulté</th><th>Barème</th><th>Statut</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($exercices as $ex): ?>
                <tr>
                    <td><?= htmlspecialchars($ex['titre']) ?></td>
                    <td><?= htmlspecialchars($ex['type']) ?></td>
                    <td><?= htmlspecialchars($ex['difficulte']) ?></td>
                    <td><?= (int)$ex['bareme'] ?> pts</td>
                    <td><span class="badge badge-statut badge-<?= $ex['statut'] ?>"><?= $ex['statut'] ?></span></td>
                    <td style="display:flex;gap:8px;flex-wrap:wrap;">
                        <a href="<?= BASE_URL ?>/admin/jeux/modifier?id=<?= $ex['id_jeux'] ?>" class="btn-action btn-edit">Modifier</a>
                        <form method="POST" action="<?= BASE_URL ?>/admin/jeux/supprimer" onsubmit="return confirm('Supprimer cet exercice ?')">
                            <input type="hidden" name="id_jeux" value="<?= $ex['id_jeux'] ?>">
                            <button type="submit" class="btn-action btn-delete">Supprimer</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table></div>
            <?php endif; ?>
            </div>
        <?php endif; ?>
        <?php endif; ?>

        <?php if ($page === 'tests'): ?>

        <?php if ($test_selectionne): ?>
            <?php $convoque = !empty($test_selectionne['est_convoque']); ?>
            <!-- Détail d'un test -->
            <div class="detail-card">
                <div class="detail-header">
                    <h2><?= htmlspecialchars($test_selectionne['titre']) ?></h2>
                    <span class="badge badge-statut badge-<?= $test_selectionne['statut'] ?>"><?= $test_selectionne['statut'] ?></span>
                </div>
                <div class="detail-meta">
                    <span><strong>Durée :</strong> <?= (int)($test_selectionne['duree_minutes'] ?? 60) ?> min</span>
                    <span><strong>Candidats :</strong> <?= (int)($test_selectionne['nb_convoques'] ?? 0) ?></span>
                    <?php if (isset($test_selectionne['date_creation'])): ?>
                    <span><strong>Créé le :</strong> <?= date('d/m/Y', strtotime($test_selectionne['date_creation'])) ?></span>
                    <?php endif; ?>
                </div>

                <?php if (!empty($jeux_test_selectionne)): ?>
                <div class="detail-section">
                    <h4>Exercices assignés</h4>
                    <ul class="detail-list">
                    <?php foreach ($jeux_test_selectionne as $j): ?>
                        <li><?= htmlspecialchars($j['titre']) ?> — <em><?= htmlspecialchars($j['type']) ?></em> (<?= (int)$j['bareme'] ?> pts)</li>
                    <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if (!empty($candidats_test_selectionne)): ?>
                <div class="detail-section">
                    <h4>Candidats convoqués</h4>
                    <ul class="detail-list">
                    <?php foreach ($candidats_test_selectionne as $c): ?>
                        <li><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?> — <?= htmlspecialchars($c['email']) ?></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <div class="detail-actions">
                    <?php if ($convoque): ?>
                        <button class="btn-action btn-admis" disabled>Convoqué ✓</button>
                    <?php else: ?>
                        <form method="POST" action="<?= BASE_URL ?>/admin/test/convoquer" style="display:inline"
                              onsubmit="return confirm('Envoyer les convocations par mail à tous les candidats de ce test ?')">
                            <input type="hidden" name="id_test" value="<?= $test_selectionne['id_test'] ?>">
                            <button type="submit" class="btn-action btn-admis">Convoquer</button>
                        </form>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>/admin/test/modifier?id=<?= $test_selectionne['id_test'] ?>" class="btn-action btn-edit">Modifier</a>
                    <form method="POST" action="<?= BASE_URL ?>/admin/test/supprimer" style="display:inline"
                          onsubmit="return confirm('Supprimer ce test et toutes ses données ?')">
                        <input type="hidden" name="id_test" value="<?= $test_selectionne['id_test'] ?>">
                        <button type="submit" class="btn-action btn-delete">Supprimer</button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <!-- Liste de tous les tests -->
            <a class="create-exo-btn" href="<?= BASE_URL ?>/admin?page=creer_test">+ Créer un test</a>
            <div class="table-wrap" style="margin-top:24px;">
                <?php if (empty($tests)): ?>
                    <p style="color:#9ca3af;text-align:center;padding:24px;">Aucun test.</p>
                <?php else: ?>
                <div class="table-container"><table>
                    <thead><tr><th>Titre</th><th>Durée</th><th>Statut</th><th>Candidats</th><th>Date création</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($tests as $t): ?>
                    <?php $convoque = !empty($t['est_convoque']); ?>
                    <tr>
                        <td><?= htmlspecialchars($t['titre']) ?></td>
                        <td><?= (int)($t['duree_minutes'] ?? 60) ?> min</td>
                        <td><span class="badge badge-statut badge-<?= $t['statut'] ?>"><?= $t['statut'] ?></span></td>
                        <td><?= (int)($t['nb_convoques'] ?? 0) ?> candidat<?= (int)($t['nb_convoques'] ?? 0) > 1 ? 's' : '' ?></td>
                        <td><?= isset($t['date_creation']) ? date('d/m/Y', strtotime($t['date_creation'])) : '–' ?></td>
                        <td style="display:flex;gap:8px;flex-wrap:wrap;">
                            <?php if ($convoque): ?>
                                <button class="btn-action btn-admis" disabled title="Convocations déjà envoyées">Convoqué ✓</button>
                            <?php else: ?>
                                <form method="POST" action="<?= BASE_URL ?>/admin/test/convoquer"
                                      onsubmit="return confirm('Envoyer les convocations par mail à tous les candidats de ce test ?')">
                                    <input type="hidden" name="id_test" value="<?= $t['id_test'] ?>">
                                    <button type="submit" class="btn-action btn-admis">Convoquer</button>
                                </form>
                            <?php endif; ?>
                            <a href="<?= BASE_URL ?>/admin/test/modifier?id=<?= $t['id_test'] ?>" class="btn-action btn-edit">Modifier</a>
                            <form method="POST" action="<?= BASE_URL ?>/admin/test/supprimer"
                                  onsubmit="return confirm('Supprimer ce test et toutes ses données ?')">
                                <input type="hidden" name="id_test" value="<?= $t['id_test'] ?>">
                                <button type="submit" class="btn-action btn-delete">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <?php endif; ?>

  <?php endif; ?>

<?php if ($page === 'modifier_exercice'): ?>
<div class="form-card">
<h2>Modifier l'exercice</h2>
<form action="<?= BASE_URL ?>/admin/jeux/modifier" method="POST">
    <input type="hidden" name="id_jeux" value="<?= (int)$jeu['id_jeux'] ?>">

    <div class="form-group">
        <label for="titre">Titre du jeu</label>
        <input type="text" name="titre" id="titre" required value="<?= htmlspecialchars($jeu['titre']) ?>">
    </div>
    <div class="form-group">
        <label for="difficulte">Difficulté</label>
        <select name="difficulte" id="difficulte" required>
            <?php foreach ($difficultes as $d): ?>
                <option value="<?= htmlspecialchars($d) ?>" <?= $jeu['difficulte'] === $d ? 'selected' : '' ?>>
                    <?= ucfirst($d) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="categorie">Type</label>
        <select name="categorie" id="categorie" required>
            <?php foreach ($types as $t): ?>
                <option value="<?= htmlspecialchars($t) ?>" <?= $jeu['type'] === $t ? 'selected' : '' ?>>
                    <?= ucfirst(str_replace('_', ' ', $t)) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="bareme">Barème (points)</label>
        <input type="number" name="bareme" id="bareme" min="1" required value="<?= (int)$jeu['bareme'] ?>">
    </div>
    <div class="form-group">
        <label for="statut">Statut</label>
        <select name="statut" id="statut">
            <?php foreach (['brouillon','actif','inactif'] as $s): ?>
                <option value="<?= $s ?>" <?= ($jeu['statut'] ?? '') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="contenu_html">Code HTML du jeu</label>
        <textarea name="contenu_html" id="contenu_html" rows="18" required
                  style="font-family:monospace;font-size:13px;"><?= htmlspecialchars($jeu['contenu_html'] ?? '') ?></textarea>
    </div>
    <div style="display:flex;gap:12px;">
        <button type="submit" class="btn-submit">Enregistrer</button>
        <a href="<?= BASE_URL ?>/admin?page=exercices" class="btn-submit" style="background:#6b7280;text-decoration:none;text-align:center;">Annuler</a>
    </div>
</form>
</div>

<?php elseif ($page === 'modifier_test'): ?>
<div class="form-card">
<h2>Modifier le test</h2>
<form action="<?= BASE_URL ?>/admin/test/modifier" method="POST">
    <input type="hidden" name="id_test" value="<?= (int)$test_edition['id_test'] ?>">

    <div class="form-group">
        <label for="titre_test">Titre du test</label>
        <input type="text" name="titre_test" id="titre_test" required value="<?= htmlspecialchars($test_edition['titre']) ?>">
    </div>
    <div class="form-group">
        <label for="duree">Durée (minutes)</label>
        <input type="number" name="duree" id="duree" min="1" required value="<?= (int)$test_edition['duree_minutes'] ?>">
    </div>
    <div class="form-group">
        <label for="statut">Statut</label>
        <select name="statut" id="statut">
            <?php foreach (['actif','inactif','brouillon','archive'] as $s): ?>
                <option value="<?= $s ?>" <?= ($test_edition['statut'] ?? '') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label>Exercices assignés</label>
        <div class="checkbox-grid">
        <?php foreach ($tous_les_jeux ?? [] as $jeu): ?>
            <label class="checkbox-item">
                <input type="checkbox" name="jeux[]" value="<?= $jeu['id_jeux'] ?>"
                    <?= in_array($jeu['id_jeux'], $ids_jeux_assignes ?? []) ? 'checked' : '' ?>>
                <?= htmlspecialchars($jeu['titre']) ?>
                <span class="badge-statut badge-<?= $jeu['statut'] ?>"><?= $jeu['statut'] ?></span>
            </label>
        <?php endforeach; ?>
        </div>
    </div>

    <div class="form-group">
        <label>
            Candidats
            <?php if ($est_convoque ?? false): ?>
                <span style="font-size:12px;color:#d62828;font-weight:400;margin-left:8px;">⚠ Convocations déjà envoyées — candidats verrouillés</span>
            <?php endif; ?>
        </label>
        <div class="checkbox-grid">
        <?php foreach ($tous_les_candidats ?? [] as $c): ?>
            <label class="checkbox-item" <?= ($est_convoque ?? false) ? 'style="opacity:0.5;pointer-events:none;"' : '' ?>>
                <input type="checkbox" name="candidats[]" value="<?= $c['id_candidat'] ?>"
                    <?= in_array($c['id_candidat'], $ids_candidats_assignes ?? []) ? 'checked' : '' ?>
                    <?= ($est_convoque ?? false) ? 'disabled' : '' ?>>
                <?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?>
                <span style="font-size:11px;color:#9ca3af;"><?= htmlspecialchars($c['email']) ?></span>
            </label>
        <?php endforeach; ?>
        </div>
    </div>

    <div style="display:flex;gap:12px;">
        <button type="submit" class="btn-submit">Enregistrer</button>
        <a href="<?= BASE_URL ?>/admin?page=tests" class="btn-submit" style="background:#6b7280;text-decoration:none;text-align:center;">Annuler</a>
    </div>
</form>
</div>

<?php elseif ($page === 'creer_exercice'): ?>

<div class="form-card">

<form action="<?= BASE_URL ?>/admin/jeux/creer" method="POST">

    <div class="form-group">
        <label for="titre">Titre du jeu</label>
        <input type="text" name="titre" id="titre" required>
    </div>

    <div class="form-group">
        <label for="difficulte">Difficulté</label>
        <select name="difficulte" id="difficulte" required>
            <?php foreach ($difficultes as $d): ?>
                <option value="<?= htmlspecialchars($d) ?>"><?= ucfirst($d) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="categorie">Type</label>
        <select name="categorie" id="categorie" required>
            <?php foreach ($types as $t): ?>
                <option value="<?= htmlspecialchars($t) ?>"><?= ucfirst(str_replace('_', ' ', $t)) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="bareme">Barème (points)</label>
        <input type="number" name="bareme" id="bareme" min="1" value="10" required>
    </div>

    <div class="form-group">
        <label for="statut">Statut</label>
        <select name="statut" id="statut">
            <option value="brouillon">Brouillon</option>
            <option value="actif">Actif</option>
            <option value="inactif">Inactif</option>
        </select>
    </div>

    <div class="form-group">
        <label for="contenu_html">Code HTML du jeu</label>
        <p style="font-size:12px;color:#00a2ff;margin-bottom:8px;">
            Le jeu doit appeler <code style="background:rgba(0,0,0,0.2);padding:2px 6px;border-radius:4px;">CodeWarden.submit(score)</code> quand le joueur termine.
        </p>
        <textarea name="contenu_html" id="contenu_html" rows="18" required
                  placeholder="Collez ici le fichier HTML complet fourni par le développeur..."
                  style="font-family:monospace;font-size:13px;"></textarea>
    </div>

    <button type="submit" class="btn-submit">Créer le jeu</button>

</form>

</div>


<?php elseif ( $page === 'creer_test'): ?>

 <div class="form-card">

        <h2>Créer un test</h2>

        <form action="<?= BASE_URL ?>/admin/test/creer" method="POST">


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
                <h3>Candidats &amp; résultats</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>E-mail</th>
                                <th>Test</th>
                                <th>Statut</th>
                                <th>Score global</th>
                                <th>Scores par jeu</th>
                                <th>Décision</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($candidats_liste)): ?>
                            <tr><td colspan="8" style="text-align:center;color:#999;">Aucun candidat convoqué.</td></tr>
                        <?php else: ?>
                            <?php foreach ($candidats_liste as $c):
                                $decision    = $c['decision'] ?? null;
                                $score       = $c['score_global'] ?? null;
                                $id_passage  = $c['id_passage_test'] ?? null;
                                $scores_jeux = $c['scores_par_jeu'] ?? null;
                                $statut      = $c['statut_passage'] ?? null;
                                $dec_colors  = ['admis'=>'badge-green','refuse'=>'badge-red','liste_attente'=>'badge-orange','en_attente'=>'badge-orange'];
                                $dec_labels  = ['admis'=>'Admis','refuse'=>'Refusé','liste_attente'=>'Liste attente','en_attente'=>'En attente'];
                                if (!$statut)          { $etat_label = 'Convoqué';   $etat_color = '#6b7280'; }
                                elseif ($statut === 'en_cours')  { $etat_label = 'En cours';  $etat_color = '#0091e6'; }
                                elseif ($statut === 'termine')   { $etat_label = 'Terminé';   $etat_color = '#0f7a2a'; }
                                elseif ($statut === 'expire')    { $etat_label = 'Expiré';    $etat_color = '#a80000'; }
                                else                             { $etat_label = $statut;      $etat_color = '#6b7280'; }
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($c['nom']) ?></td>
                                <td><?= htmlspecialchars($c['prenom']) ?></td>
                                <td><?= htmlspecialchars($c['email']) ?></td>
                                <td><?= $c['titre_test'] ? htmlspecialchars($c['titre_test']) : '–' ?></td>
                                <td><span style="font-size:12px;font-weight:600;color:<?= $etat_color ?>;"><?= $etat_label ?></span></td>
                                <td><?= $score !== null ? number_format((float)$score, 0) . ' %' : '<span style="color:#9ca3af">–</span>' ?></td>
                                <td style="font-size:12px;color:#374151;">
                                    <?php if ($scores_jeux): ?>
                                        <?php foreach (explode(' | ', $scores_jeux) as $sj): ?>
                                            <div style="white-space:nowrap;"><?= htmlspecialchars($sj) ?></div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span style="color:#9ca3af">–</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                <?php if ($id_passage && in_array($statut, ['termine','expire'])): ?>
                                    <?php $decision_finale = in_array($decision, ['admis','refuse','liste_attente']); ?>
                                    <div style="display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
                                        <span class="badge <?= $dec_colors[$decision] ?? 'badge-orange' ?>" style="margin-right:4px;">
                                            <?= $dec_labels[$decision] ?? 'En attente' ?>
                                        </span>
                                        <?php if (!$decision_finale): ?>
                                        <form method="POST" action="<?= BASE_URL ?>/admin/candidat/decision" style="display:inline">
                                            <input type="hidden" name="id_passage_test" value="<?= (int)$id_passage ?>">
                                            <input type="hidden" name="decision" value="admis">
                                            <button type="submit" class="btn-action btn-admis">Admis</button>
                                        </form>
                                        <form method="POST" action="<?= BASE_URL ?>/admin/candidat/decision" style="display:inline">
                                            <input type="hidden" name="id_passage_test" value="<?= (int)$id_passage ?>">
                                            <input type="hidden" name="decision" value="refuse">
                                            <button type="submit" class="btn-action btn-delete">Refusé</button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span style="color:#9ca3af;font-size:13px;">–</span>
                                <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($page === 'statistiques'): ?>
            <?php
            $g = $stats_globales ?? [];
            $taux = $g['taux_reussite'] ?? null;
            $taux_class = $taux === null ? 'badge-orange' : ($taux >= 70 ? 'badge-green' : ($taux >= 40 ? 'badge-orange' : 'badge-red'));
            ?>
            <div class="table-wrap">
                <h3>Vue globale</h3>
                <div class="table-container" style="margin-bottom:20px;">
                    <table>
                        <thead>
                            <tr><th>Candidats</th><th>Passages</th><th>Score moyen</th><th>Taux de réussite</th><th>Admis</th><th>Refusés</th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?= $g['nb_candidats'] ?? '–' ?></td>
                                <td><?= $g['nb_passages'] ?? '–' ?></td>
                                <td><?= $g['score_moyen'] !== null ? $g['score_moyen'] : '–' ?></td>
                                <td><span class="badge <?= $taux_class ?>"><?= $taux !== null ? $taux . '%' : '–' ?></span></td>
                                <td><?= $g['nb_admis'] ?? '–' ?></td>
                                <td><?= $g['nb_refuses'] ?? '–' ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h3>Par test</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr><th>Test</th><th>Candidats</th><th>Score moyen</th><th>Min</th><th>Max</th><th>Taux réussite</th></tr>
                        </thead>
                        <tbody>
                        <?php if (empty($stats_tests)): ?>
                            <tr><td colspan="6" style="text-align:center;color:#999;">Aucune donnée.</td></tr>
                        <?php else: ?>
                            <?php foreach ($stats_tests as $st): ?>
                            <?php
                            $t2 = $st['taux_reussite'] ?? null;
                            $tc = $t2 === null ? 'badge-orange' : ($t2 >= 70 ? 'badge-green' : ($t2 >= 40 ? 'badge-orange' : 'badge-red'));
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($st['titre']) ?></td>
                                <td><?= $st['nb_candidats'] ?></td>
                                <td><?= $st['score_moyen'] ?? '–' ?></td>
                                <td><?= $st['score_min'] ?? '–' ?></td>
                                <td><?= $st['score_max'] ?? '–' ?></td>
                                <td><span class="badge <?= $tc ?>"><?= $t2 !== null ? $t2 . '%' : '–' ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
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
        if (isNaN(nb) || nb <= 0) { updateJsonPreview(); return; }

        for (let i = 0; i < nb; i++) {
            container.appendChild(creerBlocQuestion(i));
        }
        updateJsonPreview();
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
            updateJsonPreview();
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
        updateJsonPreview();
    });

    // Mise à jour du preview JSON à chaque saisie
    document.addEventListener("input", (e) => {
        if (e.target.closest("#questions-container") || ["titre","description","bareme","difficulte","categorie","duree","statut"].includes(e.target.id)) {
            updateJsonPreview();
        }
    });

    // Appel initial pour afficher l'état vide correctement
    updateJsonPreview();

    function updateJsonPreview() {
        const preview = document.getElementById("json-preview");
        if (!preview) return;

        const questions = [];
        document.querySelectorAll(".question-block").forEach((block, qIndex) => {
            const intitule = block.querySelector(`[name="questions[${qIndex}][intitule]"]`)?.value || "";
            const points   = parseInt(block.querySelector(`[name="questions[${qIndex}][points]"]`)?.value) || 0;
            const intrus   = parseInt(block.querySelector(`[name="questions[${qIndex}][intrus]"]`)?.value ?? 0);

            const propositions = [];
            let p = 0;
            while (true) {
                const labelInput = block.querySelector(`[name="questions[${qIndex}][propositions][${p}][label]"]`);
                if (!labelInput) break;
                const imgInput = block.querySelector(`[name="questions[${qIndex}][propositions][${p}][image]"]`);
                const hasImage = imgInput && imgInput.files && imgInput.files.length > 0;
                propositions.push({
                    type:  hasImage ? "image" : "texte",
                    label: labelInput.value,
                    src:   hasImage ? "uploads/<fichier>" : null
                });
                p++;
            }

            questions.push({ intitule, propositions, intrus_index: intrus, points });
        });

        const json = {
            description:  document.getElementById("description")?.value || "",
            duree:        parseInt(document.getElementById("duree")?.value) || 0,
            nbquestions:  questions.length,
            questions
        };

        preview.textContent = JSON.stringify(json, null, 2);
    }

});
</script>

</html>