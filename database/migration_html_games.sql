-- Migration : support des jeux HTML
-- À exécuter une seule fois dans phpMyAdmin

ALTER TABLE jeux ADD COLUMN contenu_html LONGTEXT NULL AFTER contenu_json;
