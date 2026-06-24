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

<div class="layout">
    <aside class="sidebar">
        <?php if ($page === 'exercices'): ?>
            <h2>Mes exercices</h2>
            <button class="btn-create">Créer</button>
            <ul>
                <li><button class="btn-exercice">Exercice 1</button></li>
                <li><button class="btn-exercice">Exercice 2</button></li>
                <li><button class="btn-exercice">Exercice 3</button></li>
            </ul>
        <?php elseif ($page === 'tests'): ?>
            <h2>Mes tests</h2>
            <button class="btn-create">Créer</button>
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
            <button class="create-exo-btn">Créer un <?= $page === 'tests' ? 'test' : 'exercice' ?></button>

        <?php elseif ($page === 'etudiants'): ?>
            <div class="table-wrap">
                <h3>Liste des étudiants</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr><th scope="col">Nom</th><th scope="col">Prénom</th><th scope="col">Groupe</th><th scope="col">Exercices</th><th scope="col">Moyenne</th><th scope="col">Statut</th></tr>
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
                            <tr><th scope="col">Exercice</th><th scope="col">Réussite</th><th scope="col">Tentatives</th><th scope="col">Moyenne</th></tr>
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
</html>
