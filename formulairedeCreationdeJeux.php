<!DOCTYPE html>
<html lang="fr">
 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Création Jeu</title>
</head>
 
<body>
 
    <h1>Créer un jeu</h1>
 
    <form action="traitement_creation_jeux.php" method="POST">
 
        <!-- Titre -->
        <div>
            <label for="titre">Titre :</label>
            <input
                type="text"
                name="titre"
                id="titre"
                required
            >
        </div>
 
        <br>
 
        <!-- Barème -->
        <div>
            <label for="bareme">Barème :</label>
            <input
                type="number"
                name="bareme"
                id="bareme"
                min="1"
                required
            >
        </div>
 
        <br>
 
        <!-- Statut -->
        <div>
            <label for="statut">Statut :</label>
 
            <select name="statut" id="statut">
 
                <option value="actif">
                    Actif
                </option>
 
                <option value="inactif">
                    Inactif
                </option>
 
                <option value="brouillon">
                    Brouillon
                </option>
 
            </select>
        </div>
 
        <br>
 
        <!-- Bouton -->
        <button type="submit">
           créér jeu
        </button>
 
    </form>
 
</body>
 
</html>
 