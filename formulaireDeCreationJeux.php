<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="formulaireJeux.css">
</head>
<body>

<h1 class="form-title">Créer un exercice</h1>

<div class="form-card">
    <form action="traitement_creation_jeux.php" method="POST"  enctype="multipart/form-data">

        
        <div class="form-group">
            <label for="titre">Titre</label>
            <input type="text" name="titre" id="titre" required>
        </div>

         <div class="form-group">
            <label for="description">Description / Consigne</label>
            <textarea name="description" id="description" rows="4" required></textarea>
        </div>

        
        <div class="form-group">
            <label for="bareme">Barème</label>
            <input type="number" name="bareme" id="bareme" min="1" required>
        </div>

         <div class="form-group">
            <label for="difficulte">Difficulté</label>
            <select name="difficulte" id="difficulte">
                <option value="facile">Facile</option>
                <option value="moyen">Moyen</option>
                <option value="difficile">Difficile</option>
            </select>
        </div>

          <div class="form-group">
            <label for="categorie">Catégorie</label>
            <select name="categorie" id="categorie">
                <option value="algorithmique">QCM</option>
                <option value="php">Texte libre</option>
            </select>
        </div>

        <div class="form-group">
            <label for="duree">Durée estimée (secondes)</label>
            <input type="number" name="duree" id="duree" min="1">
        </div>

       
        <div class="form-group">
            <label for="statut">Statut</label>
            <select name="statut" id="statut">
                <option value="actif">Actif</option>
                <option value="inactif">Inactif</option>
                <option value="brouillon">Brouillon</option>
            </select>
        </div>

         <div class="form-group">
            <label for="duree">Nombre de questions</label>
            <input type="number" name="nbquestions" id="nbquestions" min="1">
        </div>

        <!-- Fichier -->
        <div class="form-group">
            <label for="fichier">Fichier (PDF, image, ZIP…)</label>
            <input type="file" name="fichier" id="fichier" accept=".pdf,.png,.jpg,.jpeg,.zip">
        </div>

        
        <button type="submit" class="btn-submit">Créer l'exercice</button>

    </form>
</div>

</body>
</html>