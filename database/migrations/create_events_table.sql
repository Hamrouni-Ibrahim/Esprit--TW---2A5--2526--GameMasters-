-- Table evenement
CREATE TABLE IF NOT EXISTS `evenement` (
  `idevent` int(11) NOT NULL AUTO_INCREMENT,
  `nom_evenet` varchar(255) NOT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`idevent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Migration: If table exists with old structure, add new columns and migrate data
-- ALTER TABLE `evenement` ADD COLUMN `date_debut` datetime AFTER `dateevent`;
-- ALTER TABLE `evenement` ADD COLUMN `date_fin` datetime AFTER `date_debut`;
-- UPDATE `evenement` SET `date_debut` = CONCAT(`dateevent`, ' 00:00:00'), `date_fin` = DATE_ADD(CONCAT(`dateevent`, ' 00:00:00'), INTERVAL TIME_TO_SEC(`duree`) SECOND) WHERE `date_debut` IS NULL;
-- ALTER TABLE `evenement` DROP COLUMN `dateevent`, DROP COLUMN `duree`;

-- Table participation
CREATE TABLE IF NOT EXISTS `participation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) DEFAULT '',
  `email` varchar(150) NOT NULL,
  `age` int(11) DEFAULT 0,
  `idevent` int(11) NOT NULL,
  `date_participation` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_participation_evenement` (`idevent`),
  CONSTRAINT `fk_participation_evenement` FOREIGN KEY (`idevent`) REFERENCES `evenement` (`idevent`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
