-- Add medal_notification_seen column to users table (if not exists)
-- Run this if you get "Column not found: medal_notification_seen" error

USE game_masters;

SET @dbname = DATABASE();
SET @tablename = 'users';
SET @columnname = 'medal_notification_seen';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1 AS column_exists',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' TINYINT(1) DEFAULT 1 AFTER medal')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

SELECT 'Column medal_notification_seen added or already exists' AS result;






