-- Migration for Games Library System
-- This table stores educational and social impact games accessible to logged-in users

-- Create games_library table
CREATE TABLE IF NOT EXISTS games_library (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    game_url TEXT NOT NULL,
    thumbnail_url VARCHAR(500) DEFAULT NULL,
    category VARCHAR(100) DEFAULT 'educational',
    difficulty ENUM('easy', 'medium', 'hard') DEFAULT 'easy',
    game_type ENUM('iframe', 'link', 'embed') DEFAULT 'iframe',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_difficulty (difficulty),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample games related to gaming and social impact with thumbnail images
INSERT IGNORE INTO games_library (title, description, game_url, thumbnail_url, category, difficulty, game_type) VALUES
('Climate Challenge', 'Un jeu éducatif sur le changement climatique et l\'impact environnemental. Apprenez à faire des choix durables.', 'https://www.bbc.co.uk/sn/hottopics/climatechange/climate_challenge/', 'https://images.unsplash.com/photo-1611273426858-450d8e3c9fce?w=400&h=300&fit=crop', 'social_impact', 'medium', 'link'),
('CodeCombat', 'Apprenez la programmation en jouant. Développez vos compétences en codage de manière ludique.', 'https://codecombat.com/', 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=400&h=300&fit=crop', 'educational', 'medium', 'link'),
('Scratch - Créer des Jeux', 'Plateforme pour créer et jouer à des jeux éducatifs. Développez votre créativité en programmation visuelle.', 'https://scratch.mit.edu/', 'https://images.unsplash.com/photo-1511512578047-dfb367046420?w=400&h=300&fit=crop', 'educational', 'easy', 'link'),
('Stop Disasters!', 'Jeu de simulation sur la prévention des catastrophes naturelles. Apprenez à protéger les communautés.', 'https://www.stopdisastersgame.org/', 'https://images.unsplash.com/photo-1583212292454-1fe6229603b7?w=400&h=300&fit=crop', 'social_impact', 'medium', 'link'),
('Foldit - Puzzles Scientifiques', 'Résolvez des puzzles de protéines et contribuez à la recherche scientifique. Impact social réel.', 'https://fold.it/', 'https://images.unsplash.com/photo-1532619675605-1ede6c4ed2d4?w=400&h=300&fit=crop', 'social_impact', 'hard', 'link'),
('Lightbot - Programmation', 'Jeu de puzzle pour apprendre les concepts de programmation de manière simple et amusante.', 'https://lightbot.com/flash.html', 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=400&h=300&fit=crop', 'educational', 'easy', 'link'),
('Free Rice - Éducation', 'Répondez à des questions et aidez à nourrir les personnes dans le besoin. Impact social direct.', 'https://freerice.com/', 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=400&h=300&fit=crop', 'social_impact', 'easy', 'link'),
('TypingClub - Dactylographie', 'Améliorez vos compétences en dactylographie tout en apprenant. Essentiel pour le développement.', 'https://www.typingclub.com/', 'https://images.unsplash.com/photo-1542831371-29b0f74f9713?w=400&h=300&fit=crop', 'educational', 'easy', 'link'),
('Kahoot! - Quiz Éducatifs', 'Créez et jouez à des quiz éducatifs interactifs. Apprentissage collaboratif et amusant.', 'https://kahoot.com/', 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=400&h=300&fit=crop', 'educational', 'easy', 'link'),
('Eco - Simulation Écologique', 'Jeu de simulation où vous gérez un écosystème. Apprenez l\'équilibre écologique.', 'https://www.play.eco/', 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=400&h=300&fit=crop', 'social_impact', 'hard', 'link');

-- Update existing games that might not have thumbnails (if migration was run before)
UPDATE games_library SET thumbnail_url = 'https://images.unsplash.com/photo-1611273426858-450d8e3c9fce?w=400&h=300&fit=crop' WHERE title = 'Climate Challenge' AND (thumbnail_url IS NULL OR thumbnail_url = '');
UPDATE games_library SET thumbnail_url = 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=400&h=300&fit=crop' WHERE title = 'CodeCombat' AND (thumbnail_url IS NULL OR thumbnail_url = '');
UPDATE games_library SET thumbnail_url = 'https://images.unsplash.com/photo-1511512578047-dfb367046420?w=400&h=300&fit=crop' WHERE title = 'Scratch - Créer des Jeux' AND (thumbnail_url IS NULL OR thumbnail_url = '');
UPDATE games_library SET thumbnail_url = 'https://images.unsplash.com/photo-1583212292454-1fe6229603b7?w=400&h=300&fit=crop' WHERE title = 'Stop Disasters!' AND (thumbnail_url IS NULL OR thumbnail_url = '');
UPDATE games_library SET thumbnail_url = 'https://images.unsplash.com/photo-1532619675605-1ede6c4ed2d4?w=400&h=300&fit=crop' WHERE title = 'Foldit - Puzzles Scientifiques' AND (thumbnail_url IS NULL OR thumbnail_url = '');
UPDATE games_library SET thumbnail_url = 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=400&h=300&fit=crop' WHERE title = 'Lightbot - Programmation' AND (thumbnail_url IS NULL OR thumbnail_url = '');
UPDATE games_library SET thumbnail_url = 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=400&h=300&fit=crop' WHERE title = 'Free Rice - Éducation' AND (thumbnail_url IS NULL OR thumbnail_url = '');
UPDATE games_library SET thumbnail_url = 'https://images.unsplash.com/photo-1542831371-29b0f74f9713?w=400&h=300&fit=crop' WHERE title = 'TypingClub - Dactylographie' AND (thumbnail_url IS NULL OR thumbnail_url = '');
UPDATE games_library SET thumbnail_url = 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=400&h=300&fit=crop' WHERE title = 'Kahoot! - Quiz Éducatifs' AND (thumbnail_url IS NULL OR thumbnail_url = '');
UPDATE games_library SET thumbnail_url = 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=400&h=300&fit=crop' WHERE title = 'Eco - Simulation Écologique' AND (thumbnail_url IS NULL OR thumbnail_url = '');

