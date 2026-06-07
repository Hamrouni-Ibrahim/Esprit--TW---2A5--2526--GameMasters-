-- Migration script to update evenement table from date/duree to date_debut/date_fin
-- Run this script if you already have the evenement table with the old structure

-- Step 1: Add new columns
ALTER TABLE `evenement` 
ADD COLUMN `date_debut` datetime NULL AFTER `dateevent`,
ADD COLUMN `date_fin` datetime NULL AFTER `date_debut`;

-- Step 2: Migrate existing data (convert dateevent + duree to date_debut and date_fin)
UPDATE `evenement` 
SET 
    `date_debut` = CONCAT(`dateevent`, ' 00:00:00'),
    `date_fin` = DATE_ADD(CONCAT(`dateevent`, ' 00:00:00'), INTERVAL TIME_TO_SEC(`duree`) SECOND)
WHERE `date_debut` IS NULL;

-- Step 3: Make new columns NOT NULL after data migration
ALTER TABLE `evenement`
MODIFY COLUMN `date_debut` datetime NOT NULL,
MODIFY COLUMN `date_fin` datetime NOT NULL;

-- Step 4: Remove old columns (uncomment when ready)
-- ALTER TABLE `evenement` 
-- DROP COLUMN `dateevent`,
-- DROP COLUMN `duree`;




