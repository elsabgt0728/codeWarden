-- Rattache à l'établissement existant tous les candidats inscrits avant le
-- correctif (qui avaient id_etablissement = NULL faute d'être assignés à
-- l'inscription). Sans danger à rejouer plusieurs fois : ne touche que les
-- lignes encore à NULL.

USE codewarden;

UPDATE CANDIDAT
SET id_etablissement = (SELECT id_etablissement FROM ETABLISSEMENT ORDER BY id_etablissement ASC LIMIT 1)
WHERE id_etablissement IS NULL;

-- Vérification : doit renvoyer 0
SELECT COUNT(*) AS candidats_orphelins FROM CANDIDAT WHERE id_etablissement IS NULL;
