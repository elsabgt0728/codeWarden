-- Ajoute le jeton personnel de convocation (lien envoyé par mail propre à
-- chaque candidat, au lieu d'un lien identique "?id_test=X" pour tout le monde).
-- À exécuter UNE SEULE FOIS sur une base existante.

USE codewarden;

ALTER TABLE CONVOCATION
    ADD COLUMN token VARCHAR(64) NULL UNIQUE AFTER lien_acces;

-- Les convocations déjà existantes n'ont pas de jeton : on en génère un pour
-- chacune (sinon leurs liens déjà envoyés par mail, de type "?id_test=X",
-- resteront invalides une fois le code mis à jour — à reconvoquer si besoin).
UPDATE CONVOCATION
SET token = SHA2(CONCAT(id_convocation, '-', id_candidat, '-', UUID()), 256)
WHERE token IS NULL;
