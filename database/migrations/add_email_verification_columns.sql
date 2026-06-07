-- Script SQL pour ajouter les colonnes de vérification d'email et réinitialisation de mot de passe

-- Ajouter les colonnes pour la vérification d'email
ALTER TABLE `users` 
ADD COLUMN `email_verified` TINYINT(1) DEFAULT 0 AFTER `status`,
ADD COLUMN `verification_code` VARCHAR(10) NULL AFTER `email_verified`,
ADD COLUMN `reset_code` VARCHAR(64) NULL AFTER `verification_code`,
ADD COLUMN `reset_code_expires` DATETIME NULL AFTER `reset_code`;

-- Créer un index sur verification_code pour améliorer les performances
CREATE INDEX `idx_verification_code` ON `users` (`verification_code`);

-- Créer un index sur reset_code pour améliorer les performances
CREATE INDEX `idx_reset_code` ON `users` (`reset_code`);

-- Mettre à jour les utilisateurs existants comme vérifiés (optionnel)
-- UPDATE `users` SET `email_verified` = 1 WHERE `email_verified` IS NULL;
