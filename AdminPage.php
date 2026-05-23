<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>CodeWarden</title>
  <link href="CodeWardenAdminCSS.css" rel="stylesheet">
</head>
<body>

<?php
$page = isset($_GET['page']) ? $_GET['page'] : 'exercices';
?>

  <header>
    <div class="header-inner">
      <div class="nav-group">
        <a class="navbar-link <?= $page === 'exercices' ? 'active' : '' ?>" href="?page=exercices">Exercices</a>
        <a class="navbar-link <?= $page === 'tests' ? 'active' : '' ?>" href="?page=tests">Tests</a>
      </div>
      <div class="header-logo">CodeWarden</div>
      <div class="nav-group">
        <a class="navbar-link <?= $page === 'etudiants' ? 'active' : '' ?>" href="?page=etudiants">Étudiants</a>
        <a class="navbar-link <?= $page === 'statistiques' ? 'active' : '' ?>" href="?page=statistiques">Statistiques</a>
      </div>
    </div>
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
          <li><button class="btn-exercice">Exercice 4</button></li>
          <li><button class="btn-exercice">Exercice 5</button></li>
        </ul>

      <?php elseif ($page === 'tests'): ?>
        <h2>Mes tests</h2>
        <button class="btn-create">Créer</button>
        <ul>
          <li><button class="btn-exercice">Test 1</button></li>
          <li><button class="btn-exercice">Test 2</button></li>
          <li><button class="btn-exercice">Test 3</button></li>
        </ul>

      <?php elseif ($page === 'etudiants'): ?>
        <h2>Étudiants</h2>
        <button class="btn-create">Ajouter</button>
        <ul>
          <li><button class="btn-exercice">Groupe A</button></li>
          <li><button class="btn-exercice">Groupe B</button></li>
          <li><button class="btn-exercice">Groupe C</button></li>
        </ul>

      <?php elseif ($page === 'statistiques'): ?>
        <h2>Statistiques</h2>
        <ul>
          <li><button class="btn-exercice">Test 1</button></li>
          <li><button class="btn-exercice">Test 2</button></li>
          <li><button class="btn-exercice">Test 3</button></li>
        </ul>
      <?php endif; ?>
    </aside>

    <main class="content-center">
      <?php if ($page === 'exercices'): ?>
        <button class="create-exo-btn">Créer un exercice</button>

      <?php elseif ($page === 'tests'): ?>
        <button class="create-exo-btn">Créer un test</button>

        <?php elseif ($page === 'etudiants'): ?>
        <div class="table-wrap">
          <h3>Liste des étudiants</h3>
          <div class="table-container">
            <table>
              <thead>
                <tr>
                  <th>Nom</th>
                  <th>Prénom</th>
                  <th>Groupe</th>
                  <th>Exercices complétés</th>
                  <th>Moyenne</th>
                  <th>Statut</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Dupont</td><td>Alice</td><td>Groupe A</td><td>12</td><td>15/20</td>
                  <td><span class="badge badge-green">Actif</span></td>
                </tr>
                <tr>
                  <td>Martin</td><td>Bob</td><td>Groupe B</td><td>8</td><td>11/20</td>
                  <td><span class="badge badge-green">Actif</span></td>
                </tr>
                <tr>
                  <td>Bernard</td><td>Clara</td><td>Groupe A</td><td>5</td><td>9/20</td>
                  <td><span class="badge badge-red">Inactif</span></td>
                </tr>
                <tr>
                  <td>Leroy</td><td>David</td><td>Groupe C</td><td>14</td><td>17/20</td>
                  <td><span class="badge badge-green">Actif</span></td>
                </tr>
                <tr>
                  <td>Moreau</td><td>Emma</td><td>Groupe B</td><td>10</td><td>13/20</td>
                  <td><span class="badge badge-green">Actif</span></td>
                </tr>
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
                <tr>
                  <th>Exercice</th>
                  <th>Taux de réussite</th>
                  <th>Tentatives</th>
                  <th>Moyenne</th>
                  <th>Meilleur score</th>
                  <th>Pire score</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Exercice 1</td>
                  <td><span class="badge badge-green">87%</span></td>
                  <td>45</td><td>14.2/20</td><td>20/20</td><td>8/20</td>
                </tr>
                <tr>
                  <td>Exercice 2</td>
                  <td><span class="badge badge-orange">63%</span></td>
                  <td>38</td><td>11.5/20</td><td>19/20</td><td>4/20</td>
                </tr>
                <tr>
                  <td>Exercice 3</td>
                  <td><span class="badge badge-red">41%</span></td>
                  <td>52</td><td>9.1/20</td><td>18/20</td><td>2/20</td>
                </tr>
                <tr>
                  <td>Exercice 4</td>
                  <td><span class="badge badge-green">79%</span></td>
                  <td>41</td><td>13.8/20</td><td>20/20</td><td>7/20</td>
                </tr>
                <tr>
                  <td>Exercice 5</td>
                  <td><span class="badge badge-orange">55%</span></td>
                  <td>29</td><td>10.3/20</td><td>17/20</td><td>3/20</td>
                </tr>
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
      <a href="#">Policy</a>
      <a href="#">Terms</a>
    </div>
    <div class="footer-center">
      <strong>CodeWarden</strong>
      <span>Be sure to take a look at our Terms of Use</span>
      <span>and Privacy Policy</span>
    </div>
    <div class="footer-right">
      <div>Contacts</div>
      <div class="social-icons">
        <span>T</span><span>F</span><span>G+</span>
      </div>
    </div>
  </footer>

</body>
</html>
