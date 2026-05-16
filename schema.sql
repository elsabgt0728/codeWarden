-- créer la base de données complète

DROP DATABASE IF EXISTS codewarden;

CREATE DATABASE
    IF NOT EXISTS codewarden DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE codewarden;

-- TABLE : ETABLISSEMENT

CREATE TABLE
    ETABLISSEMENT (
        -- pour le test, id_etablissement null mais à modifier quand le formulaire sera modifié
        id_etablissement INT PRIMARY KEY AUTO_INCREMENT  NULL,
        nom VARCHAR(255) NOT NULL,
        logo VARCHAR(255),
        email_contact VARCHAR(255) NOT NULL,
        couleur VARCHAR(50)
    );

-- TABLE : GROUPE

CREATE TABLE
    GROUPE (
        id_groupe INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
        nom VARCHAR(255) NOT NULL,
        promotion VARCHAR(255),
        annee YEAR,
        id_etablissement INT NOT NULL,
        FOREIGN KEY (id_etablissement) REFERENCES ETABLISSEMENT (id_etablissement) ON UPDATE CASCADE ON DELETE RESTRICT
    );

-- TABLE : CANDIDAT

CREATE TABLE
    CANDIDAT (
        id_candidat INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
        nom VARCHAR(255) NOT NULL,
        prenom VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        date_naissance DATE,
        code_acces VARCHAR(100),
        statut ENUM('actif', 'inactif', 'suspendu') NOT NULL DEFAULT 'actif',
        
         -- pour le test, id_etablissement null mais à modifier quand le formulaire sera modifié
        id_etablissement INT  NULL,
        FOREIGN KEY (id_etablissement) REFERENCES ETABLISSEMENT (id_etablissement) ON UPDATE CASCADE ON DELETE RESTRICT
    );

-- TABLE : CANDIDAT_GROUPE  (association Candidat ↔ Groupe)

CREATE TABLE
    CANDIDAT_GROUPE (
        id_candidat_groupe INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
        id_candidat INT NOT NULL,
        id_groupe INT NOT NULL,
        FOREIGN KEY (id_candidat) REFERENCES CANDIDAT (id_candidat) ON UPDATE CASCADE ON DELETE CASCADE,
        FOREIGN KEY (id_groupe) REFERENCES GROUPE (id_groupe) ON UPDATE CASCADE ON DELETE CASCADE
    );

-- TABLE : ADMINISTRATEUR

CREATE TABLE
    ADMINISTRATEUR (
        id_admin INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
        nom VARCHAR(255) NOT NULL,
        prenom VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        mot_de_passe VARCHAR(255) NOT NULL,
        role ENUM(
            'super_admin',
            'admin',
            'moderateur'
        ) NOT NULL DEFAULT 'admin',
        id_etablissement INT NOT NULL,
        FOREIGN KEY (id_etablissement) REFERENCES ETABLISSEMENT (id_etablissement) ON UPDATE CASCADE ON DELETE CASCADE
    );

-- TABLE : JEUX

CREATE TABLE
    JEUX (
        id_jeux INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
        titre VARCHAR(255) NOT NULL,
        type ENUM(
            'qcm',
            'texte_libre',
            'glisser_deposer',
            'association',
            'autre'
        ) NOT NULL,
        difficulte ENUM(
            'facile',
            'moyen',
            'difficile',
            'expert'
        ) NOT NULL DEFAULT 'moyen',
        bareme INT NOT NULL DEFAULT 1,
        contenu_json JSON NULL,
        statut ENUM(
            'actif',
            'inactif',
            'brouillon'
        ) NOT NULL DEFAULT 'brouillon',
        id_admin INT NOT NULL,
        FOREIGN KEY (id_admin) REFERENCES ADMINISTRATEUR (id_admin) ON UPDATE CASCADE ON DELETE RESTRICT
    );

-- TABLE : TEST

CREATE TABLE
    TEST (
        id_test INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
        titre VARCHAR(255) NOT NULL,
        duree_minutes INT NOT NULL DEFAULT 60,
        ordre_aleatoire BOOLEAN NOT NULL DEFAULT FALSE,
        acces_code VARCHAR(100) NULL,
        statut ENUM(
            'actif',
            'inactif',
            'brouillon',
            'archive'
        ) NOT NULL DEFAULT 'brouillon',
        date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        id_admin INT NOT NULL,
        FOREIGN KEY (id_admin) REFERENCES ADMINISTRATEUR (id_admin) ON UPDATE CASCADE ON DELETE RESTRICT
    );

-- TABLE : TEST_JEUX  (association Test ↔ Jeux)

CREATE TABLE
    TEST_JEUX (
        id_test_jeux INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
        position INT NOT NULL DEFAULT 1,
        points_max INT NOT NULL DEFAULT 1,
        id_test INT NOT NULL,
        id_jeux INT NOT NULL,
        FOREIGN KEY (id_test) REFERENCES TEST (id_test) ON UPDATE CASCADE ON DELETE CASCADE,
        FOREIGN KEY (id_jeux) REFERENCES JEUX (id_jeux) ON UPDATE CASCADE ON DELETE CASCADE
    );

-- TABLE : SESSION

CREATE TABLE
    SESSION (
        id_session INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
        date_debut DATETIME NOT NULL,
        date_fin DATETIME NULL,
        statut ENUM(
            'planifiee',
            'en_cours',
            'terminee',
            'annulee'
        ) NOT NULL DEFAULT 'planifiee',
        id_test INT NOT NULL,
        id_admin INT NOT NULL,
        FOREIGN KEY (id_test) REFERENCES TEST (id_test) ON UPDATE CASCADE ON DELETE RESTRICT,
        FOREIGN KEY (id_admin) REFERENCES ADMINISTRATEUR (id_admin) ON UPDATE CASCADE ON DELETE RESTRICT
    );

-- TABLE : NOTIFICATION

CREATE TABLE
    NOTIFICATION (
        id_notification INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
        type ENUM(
            'convocation',
            'rappel',
            'resultat',
            'information',
            'alerte'
        ) NOT NULL,
        contenu TEXT NOT NULL,
        date_envoi DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        statut ENUM(
            'en_attente',
            'envoye',
            'echec',
            'lu'
        ) NOT NULL DEFAULT 'en_attente',
        id_candidat INT NOT NULL,
        id_admin INT NOT NULL,
        FOREIGN KEY (id_candidat) REFERENCES CANDIDAT (id_candidat) ON UPDATE CASCADE ON DELETE CASCADE,
        FOREIGN KEY (id_admin) REFERENCES ADMINISTRATEUR (id_admin) ON UPDATE CASCADE ON DELETE RESTRICT
    );

-- TABLE : CONVOCATION

CREATE TABLE
    CONVOCATION (
        id_convocation INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
        date_envoi DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        lien_acces VARCHAR(500) NULL,
        date_expiration DATETIME NULL,
        id_candidat INT NOT NULL,
        id_session INT NOT NULL,
        FOREIGN KEY (id_candidat) REFERENCES CANDIDAT (id_candidat) ON UPDATE CASCADE ON DELETE CASCADE,
        CONSTRAINT FK_CONV_SESSION FOREIGN KEY (id_session) REFERENCES SESSION (id_session) ON UPDATE CASCADE ON DELETE CASCADE
    );

-- TABLE : PASSAGE_TEST

CREATE TABLE
    PASSAGE_TEST (
        id_passage_test INT PRIMARY KEY AUTO_INCREMENT NOT NULL UNIQUE,
        date_debut DATETIME NOT NULL,
        date_fin DATETIME NULL,
        statut ENUM(
            'en_cours',
            'termine',
            'abandonne',
            'expire'
        ) NOT NULL DEFAULT 'en_cours',
        score_total DECIMAL(10, 2) NULL DEFAULT 0.00,
        id_candidat INT NOT NULL,
        id_session INT NOT NULL,
        FOREIGN KEY (id_candidat) REFERENCES CANDIDAT (id_candidat) ON UPDATE CASCADE ON DELETE CASCADE,
        FOREIGN KEY (id_session) REFERENCES SESSION (id_session) ON UPDATE CASCADE ON DELETE RESTRICT
    );

-- TABLE : REPONSE

CREATE TABLE
    REPONSE (
        id_reponse INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
        reponse_donnee TEXT NULL,
        est_correcte BOOLEAN NOT NULL DEFAULT FALSE,
        points_obtenus DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
        temps_reponse_sec INT NULL,
        id_passage_test INT NOT NULL,
        id_jeux INT NOT NULL,
        FOREIGN KEY (id_passage_test) REFERENCES PASSAGE_TEST (id_passage_test) ON UPDATE CASCADE ON DELETE CASCADE,
        FOREIGN KEY (id_jeux) REFERENCES JEUX (id_jeux) ON UPDATE CASCADE ON DELETE RESTRICT
    );

-- TABLE : RESULTAT

CREATE TABLE
    RESULTAT (
        id_resultat INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
        score_global DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
        rang INT NULL,
        decision ENUM(
            'admis',
            'refuse',
            'liste_attente',
            'en_attente'
        ) NOT NULL DEFAULT 'en_attente',
        afficher_score BOOLEAN NOT NULL DEFAULT FALSE,
        afficher_corrige BOOLEAN NOT NULL DEFAULT FALSE,
        id_passage_test INT NOT NULL UNIQUE,
        FOREIGN KEY (id_passage_test) REFERENCES PASSAGE_TEST (id_passage_test) ON UPDATE CASCADE ON DELETE CASCADE
    );
