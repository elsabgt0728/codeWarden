-- Données d'amorçage : un établissement + un super_admin de test
-- Mot de passe en clair (à changer) : Admin1234

USE codewarden;

-- ETABLISSEMENT de test
INSERT INTO ETABLISSEMENT (nom, email_contact, couleur)
VALUES ('Établissement de test', 'contact@codewarden.test', '#2563EB');

-- super_admin de test (mot de passe : Admin1234, haché en bcrypt)
INSERT INTO ADMINISTRATEUR (nom, prenom, email, mot_de_passe, role, id_etablissement)
VALUES (
    'Admin',
    'Test',
    'admin@codewarden.test',
    '$2y$10$Ns2Q3D8WZb/Ec994M3AZqeDwLAtST60LJTmSiw9OWq4gcVbzDzrRK',
    'super_admin',
    (SELECT id_etablissement FROM ETABLISSEMENT WHERE email_contact = 'contact@codewarden.test' LIMIT 1)
);

ALTER TABLE JEUX
ADD COLUMN description TEXT NULL AFTER titre;
