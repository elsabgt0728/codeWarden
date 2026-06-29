function setActive(el) {
    document.querySelectorAll('.cw-nav-item').forEach(i => i.classList.remove('active'));
    el.classList.add('active');
}
  
document.getElementById("creation_test").addEventListener("submit", function (e) {

let hasError = false;

const titre = document.getElementById("titre").value.trim();
const duree = document.getElementById("duree").value;
const description = document.getElementById("description").value.trim();

const titreError = document.getElementById("titre_error");
const dureeError = document.getElementById("duree_error");
const descriptionError = document.getElementById("description_error");
const jeuxError = document.getElementById("jeux_error");

// reset erreurs
titreError.textContent = "";
dureeError.textContent = "";
descriptionError.textContent = "";
jeuxError.textContent = "";

// ===== TITRE =====
if (titre === "") {
titreError.textContent = "Le titre est obligatoire.";
hasError = true;
}

// ===== DURÉE =====
if (duree === "") {
dureeError.textContent = "La durée est obligatoire.";
hasError = true;
} else if (isNaN(duree) || duree <= 0) {
dureeError.textContent = "La durée doit être un nombre supérieur à 0.";
hasError = true;
}

// ===== DESCRIPTION =====
if (description === "") {
descriptionError.textContent = "La description est obligatoire.";
hasError = true;
}

// ===== JEUX =====
const checkboxes = document.querySelectorAll("input[name='jeux[]']");
let checked = false;

checkboxes.forEach(cb => {
if (cb.checked) checked = true;
});

if (!checked) {
jeuxError.textContent = "Veuillez sélectionner au moins un jeu.";
hasError = true;
}

// ===== BLOQUER ENVOI =====
if (hasError) {
e.preventDefault();
}

});