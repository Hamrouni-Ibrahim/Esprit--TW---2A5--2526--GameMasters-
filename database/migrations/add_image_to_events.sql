-- Migration: Add image column to evenement table
ALTER TABLE `evenement` ADD COLUMN `image` VARCHAR(255) NULL DEFAULT NULL AFTER `description`;


