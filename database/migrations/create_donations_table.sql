-- Migration: Create donations table
-- This table stores donation information

CREATE TABLE IF NOT EXISTS `donations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `amount` float DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `project_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_donations_project` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Note: If projects table exists, you can add foreign key constraint:
-- ALTER TABLE `donations`
--   ADD CONSTRAINT `fk_donations_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;




