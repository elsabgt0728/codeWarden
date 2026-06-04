codewarden/
│
├── index.php                        ← point d'entrée unique (toutes les requêtes passent ici)
├── .htaccess                        ← redirige toutes les URLs vers index.php
│
├── routes/
│   └── web.php                      ← associe chaque URL à une fonction
│
├── config/
│   └── config.php                   ← paramètres de connexion à la base de données
│
├── database/
│   └── schema.sql                   ← structure de la base de données (tables)
│
├── app/
│   │
│   ├── core/                        ← fonctions de base utilisées partout
│   │   ├── database.php             ← connecter_bdd() : connexion PDO à MySQL
│   │   └── helpers.php              ← afficher_vue()
│   │
│   ├── models/                      ← fonctions qui lisent/écrivent en base de données
│   │   ├── candidat.php             ← trouver_candidat_par_email(), creer_candidat()...
│   │   └── admin.php                ← trouver_admin_par_email()...
│   │
│   ├── middlewares/
│   │   └── auth.php                 ← verifier_candidat(), verifier_admin()
│   │
│   ├── controllers/                 ← fonctions appelées selon l'URL visitée
│   │   ├── auth_controller.php      ← page_connexion(), traiter_connexion(), page_inscription()...
│   │   ├── candidat_controller.php  ← page_profil(), modifier_profil()
│   │   └── admin_controller.php     ← tableau_de_bord(), traiter_connexion_admin()...
│   │
│   └── views/                       ← fichiers HTML affichés à l'utilisateur
│       ├── auth/
│       │   ├── login.php            ← page de connexion candidat
│       │   └── signup.php           ← page d'inscription candidat
│       ├── candidat/
│       │   └── profile.php          ← page de profil candidat
│       └── admin/
│           ├── login.php            ← page de connexion admin
│           └── dashboard.php        ← tableau de bord admin
│
└── public/
    └── assets/
        ├── css/
        │   ├── style.css                ← styles espace candidat
        │   └── CodeWardenAdminCSS.css   ← styles espace admin
        └── js/
            └── validation.js            ← validation des formulaires
