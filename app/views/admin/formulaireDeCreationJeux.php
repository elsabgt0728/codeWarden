<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/FormulaireJeux.css">
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

        <div id="questions-container"></div>


        <button type="submit" class="btn-submit">Créer l'exercice</button>

    </form>
</div>

<script>

document.getElementById("nbquestions").addEventListener("change", function() {
    const nb = parseInt(this.value);
    const container = document.getElementById("questions-container");
    container.innerHTML = "";

    for (let i = 1; i <= nb; i++) {
        container.innerHTML += `
            <div class="question-block">
                <h3>Question ${i}</h3>

                <div class="form-group">
                    <label>Intitulé</label>
                    <textarea name="question_intitule_${i}" rows="2" required></textarea>
                </div>

                <div class="form-group">
                    <label>Image (optionnel)</label>
                    <input type="file" name="question_image_${i}[]" accept="image/*,.pdf,.png,.jpg,.jpeg,.zip">
                </div>

                <div class="form-group">
                    <label>Points</label>
                    <input type="number" name="question_points_${i}" min="1" required>
                </div>

                <div class="form-group">
                    <label>Type</label>
                    <select name="question_type_${i}">
                        <option value="texte">Réponse texte</option>
                        <option value="qcm">QCM</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Bonne réponse (si QCM)</label>
                    <input type="text" name="question_bonne_${i}">
                </div>
            </div>
        `;
    }
});
</script>


</body>
</html>