// Formulaire d'inscription
document.getElementById("signup-form").addEventListener("submit", function(event) {

    const prenom   = document.getElementById("prenom").value.trim();
    const nom      = document.getElementById("nom").value.trim();
    const email    = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value;
    const confirm  = document.getElementById("confirm").value;
    let erreur   = false;

    document.getElementById("err-prenom").textContent  = "";
    document.getElementById("err-nom").textContent     = "";
    document.getElementById("err-email").textContent   = "";
    document.getElementById("err-password").textContent = "";
    document.getElementById("err-confirm").textContent = "";

    if (prenom === "") {
        document.getElementById("err-prenom").textContent = "Le prénom est obligatoire.";
        erreur = true;
    }

    if (nom === "") {
        document.getElementById("err-nom").textContent = "Le nom est obligatoire.";
        erreur = true;
    }

    if (email === "") {
        document.getElementById("err-email").textContent = "L'e-mail est obligatoire.";
        erreur = true;
    } else if (!email.includes("@") || !email.includes(".")) {
        document.getElementById("err-email").textContent = "E-mail invalide.";
        erreur = true;
    }

    if (password === "") {
        document.getElementById("err-password").textContent = "Le mot de passe est obligatoire.";
        erreur = true;
    } else if (password.length < 8) {
        document.getElementById("err-password").textContent = "Minimum 8 caractères.";
        erreur = true;
    }

    if (confirm === "") {
        document.getElementById("err-confirm").textContent = "Veuillez confirmer le mot de passe.";
        erreur = true;
    } else if (password !== confirm) {
        document.getElementById("err-confirm").textContent = "Les mots de passe ne correspondent pas.";
        erreur = true;
    }

    if (erreur) {
        event.preventDefault();
    }
});


// Formulaire de connexion
document.getElementById("login-form").addEventListener("submit", function(event) {

    const email    = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value;
    let erreur     = false;

    document.getElementById("err-email").textContent    = "";
    document.getElementById("err-password").textContent = "";

    if (email === "") {
        document.getElementById("err-email").textContent = "L'e-mail est obligatoire.";
        erreur = true;
    } else if (!email.includes("@") || !email.includes(".")) {
        document.getElementById("err-email").textContent = "E-mail invalide.";
        erreur = true;
    }

    if (password === "") {
        document.getElementById("err-password").textContent = "Le mot de passe est obligatoire.";
        erreur = true;
    }

    if (erreur) {
        event.preventDefault();
    }
});


// Formulaire de profil
document.getElementById("profile-form").addEventListener("submit", function(event) {

    const prenom = document.getElementById("prenom").value.trim();
    const nom    = document.getElementById("nom").value.trim();
    const email  = document.getElementById("email").value.trim();
    let erreur   = false;

    document.getElementById("err-prenom").textContent = "";
    document.getElementById("err-nom").textContent    = "";
    document.getElementById("err-email").textContent  = "";

    if (prenom === "") {
        document.getElementById("err-prenom").textContent = "Le prénom est obligatoire.";
        erreur = true;
    }

    if (nom === "") {
        document.getElementById("err-nom").textContent = "Le nom est obligatoire.";
        erreur = true;
    }

    if (email === "") {
        document.getElementById("err-email").textContent = "L'e-mail est obligatoire.";
        erreur = true;
    } else if (!email.includes("@") || !email.includes(".")) {
        document.getElementById("err-email").textContent = "E-mail invalide.";
        erreur = true;
    }

    if (erreur) {
        event.preventDefault();
    }
});
