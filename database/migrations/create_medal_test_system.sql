-- Migration for Medal and QCM Test System
-- Run this script in your database

USE game_masters;

-- 1. Add medal column to users table (if not exists)
SET @dbname = DATABASE();
SET @tablename = 'users';
SET @columnname = 'medal';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' ENUM(\'none\', \'bronze\', \'silver\', \'gold\') DEFAULT \'none\' AFTER status')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add medal_notification_seen column (if not exists)
SET @columnname2 = 'medal_notification_seen';
SET @preparedStatement2 = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname2)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname2, ' TINYINT(1) DEFAULT 1 AFTER medal')
));
PREPARE alterIfNotExists2 FROM @preparedStatement2;
EXECUTE alterIfNotExists2;
DEALLOCATE PREPARE alterIfNotExists2;

-- 2. Create test_requests table (users request permission to take test)
CREATE TABLE IF NOT EXISTS test_requests (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    motivational_letter TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    admin_response TEXT NULL,
    admin_id INT(11) NULL COMMENT 'Admin who reviewed the request',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY unique_user_pending (user_id, status),
    KEY idx_user_id (user_id),
    KEY idx_status (status),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Create test_questions table (QCM questions)
CREATE TABLE IF NOT EXISTS test_questions (
    id INT(11) NOT NULL AUTO_INCREMENT,
    question TEXT NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_d VARCHAR(255) NOT NULL,
    correct_answer ENUM('a', 'b', 'c', 'd') NOT NULL,
    explanation TEXT NULL COMMENT 'Explanation of the correct answer',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Create test_attempts table (user test attempts)
CREATE TABLE IF NOT EXISTS test_attempts (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    test_request_id INT(11) NOT NULL,
    score DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Percentage score (0-100)',
    total_questions INT(11) DEFAULT 0,
    correct_answers INT(11) DEFAULT 0,
    time_taken INT(11) DEFAULT 0 COMMENT 'Time taken in seconds',
    time_limit INT(11) DEFAULT 1800 COMMENT 'Time limit in seconds (30 minutes)',
    status ENUM('in_progress', 'completed', 'expired') DEFAULT 'in_progress',
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    submitted_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    KEY idx_user_id (user_id),
    KEY idx_test_request_id (test_request_id),
    KEY idx_status (status),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (test_request_id) REFERENCES test_requests(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Create test_answers table (individual answers to questions)
CREATE TABLE IF NOT EXISTS test_answers (
    id INT(11) NOT NULL AUTO_INCREMENT,
    test_attempt_id INT(11) NOT NULL,
    question_id INT(11) NOT NULL,
    user_answer ENUM('a', 'b', 'c', 'd') NULL,
    is_correct TINYINT(1) DEFAULT 0,
    answered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY unique_attempt_question (test_attempt_id, question_id),
    KEY idx_test_attempt_id (test_attempt_id),
    KEY idx_question_id (question_id),
    FOREIGN KEY (test_attempt_id) REFERENCES test_attempts(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES test_questions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Create test_approvals table (admin approves test results)
CREATE TABLE IF NOT EXISTS test_approvals (
    id INT(11) NOT NULL AUTO_INCREMENT,
    test_attempt_id INT(11) NOT NULL,
    admin_id INT(11) NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    admin_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY unique_test_attempt (test_attempt_id),
    KEY idx_admin_id (admin_id),
    KEY idx_status (status),
    FOREIGN KEY (test_attempt_id) REFERENCES test_attempts(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Insert sample QCM questions about gaming and social impact (ignore if already exist)
INSERT IGNORE INTO test_questions (question, option_a, option_b, option_c, option_d, correct_answer, explanation, is_active) VALUES
('Quelle entreprise a développé le jeu "Minecraft" qui a un fort impact éducatif ?', 'Epic Games', 'Mojang Studios', 'Valve Corporation', 'Ubisoft', 'b', 'Minecraft a été développé par Mojang Studios (maintenant propriété de Microsoft) et est largement utilisé dans l\'éducation pour développer la créativité et les compétences en programmation.', 1),

('Quel est l\'un des principaux avantages de l\'apprentissage du développement de jeux vidéo ?', 'Améliorer uniquement les compétences artistiques', 'Développer la pensée logique, la résolution de problèmes et la créativité', 'Ne nécessite aucune compétence technique', 'Limite la communication', 'b', 'Le développement de jeux combine la programmation, la logique, la créativité et le travail d\'équipe, développant des compétences multiples.', 1),

('Quel jeu est connu pour son impact social positif en sensibilisant à l\'écologie ?', 'Call of Duty', 'Terra Nil', 'Grand Theft Auto', 'FIFA', 'b', 'Terra Nil est un jeu de stratégie qui enseigne la restauration écologique et la protection de l\'environnement.', 1),

('Quelle compétence technique est essentielle pour développer des jeux vidéo ?', 'Cuisiner', 'Programmation/Codage', 'Peindre', 'Chanter', 'b', 'La programmation est fondamentale pour créer des jeux vidéo, permettant de définir les mécaniques, les interactions et la logique du jeu.', 1),

('Quel est l\'impact social positif des jeux éducatifs sur la société ?', 'Aucun impact', 'Ils peuvent améliorer l\'apprentissage et développer des compétences cognitives', 'Ils encouragent uniquement la compétition', 'Ils isolent les joueurs', 'b', 'Les jeux éducatifs peuvent rendre l\'apprentissage plus engageant, améliorer la rétention des connaissances et développer des compétences essentielles.', 1),

('Quelle plateforme est souvent utilisée pour apprendre le développement de jeux débutants ?', 'Unity', 'Microsoft Word', 'Adobe Photoshop', 'Excel', 'a', 'Unity est un moteur de jeu populaire utilisé pour l\'apprentissage du développement de jeux, particulièrement pour les débutants.', 1),

('Quel type de jeu peut avoir le plus grand impact social positif ?', 'Jeux violents uniquement', 'Jeux éducatifs et jeux avec messages sociaux', 'Jeux de casino', 'Aucun jeu', 'b', 'Les jeux éducatifs et ceux avec des messages sociaux positifs peuvent sensibiliser, éduquer et inspirer le changement social.', 1),

('Quelle entreprise a développé "Kind Words", un jeu axé sur l\'empathie et le bien-être mental ?', 'Electronic Arts', 'Popcannibal', 'Activision', 'Nintendo', 'b', 'Kind Words a été développé par Popcannibal et se concentre sur l\'échange de messages positifs et le soutien émotionnel entre joueurs.', 1),

('Pourquoi est-il important d\'apprendre le codage dans le contexte du développement de jeux ?', 'C\'est inutile', 'Cela permet de créer des mécaniques de jeu, des interactions et des systèmes complexes', 'Seuls les graphistes sont nécessaires', 'Les jeux n\'ont pas besoin de code', 'b', 'Le codage est essentiel pour créer la logique du jeu, les interactions, l\'IA, les systèmes de score et toutes les mécaniques de jeu.', 1),

('Quel est un avantage du développement de jeux pour les jeunes apprenants ?', 'Limite la créativité', 'Développe la patience, la persévérance et les compétences en résolution de problèmes', 'Décourage la collaboration', 'Ne nécessite aucune pensée', 'b', 'Le développement de jeux enseigne la patience face aux bugs, la persévérance pour résoudre des problèmes complexes et encourage le travail collaboratif.', 1);

