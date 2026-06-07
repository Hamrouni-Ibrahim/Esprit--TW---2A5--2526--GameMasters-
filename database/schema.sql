-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 13, 2025 at 02:07 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `game_masters`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `nom`, `created_at`) VALUES
(12, 'test', '2025-11-24 20:57:14'),
(13, 'data save', '2025-11-25 12:25:11'),
(14, 'ibrahim', '2025-11-25 12:26:55'),
(15, 'programming', '2025-11-25 12:49:37'),
(16, 'graphic', '2025-11-25 13:51:30'),
(17, 'eh', '2025-11-25 20:20:14'),
(18, 'bb', '2025-11-25 20:27:53'),
(19, 'ee', '2025-11-27 19:49:19'),
(20, 'bla', '2025-11-27 19:50:44'),
(21, 'ez', '2025-12-01 08:33:14'),
(22, 'cdcdcd', '2025-12-01 08:34:06'),
(23, 'khalil', '2025-12-01 19:36:55'),
(26, 'wwww', '2025-12-01 20:32:55'),
(27, 'bbbbbbbbbbb', '2025-12-01 21:48:25'),
(28, 'rafff', '2025-12-02 20:38:06'),
(29, 'bleza', '2025-12-02 20:38:44'),
(30, 'pdo', '2025-12-10 12:42:28');

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `amount` float DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `project_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`id`, `name`, `email`, `amount`, `date`, `created_at`, `project_id`) VALUES
(3, 'ahmed', 'ahmed@gmail.com', 5456460, '2025-12-11 20:56:51', '2025-12-11 21:56:51', 1),
(4, 'ahmed', 'ahmed@gmail.com', 5454540, '2025-12-11 21:07:24', '2025-12-11 22:07:24', 2);

-- --------------------------------------------------------

--
-- Table structure for table `educations`
--

CREATE TABLE `educations` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `competences` text DEFAULT NULL,
  `difficulte` enum('Débutant','Intermédiaire','Avancé') DEFAULT 'Débutant',
  `duree` int(11) DEFAULT 0,
  `prerequis` text DEFAULT NULL,
  `categorie` varchar(255) DEFAULT NULL,
  `lien_ressources` varchar(500) DEFAULT NULL,
  `impact_social` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `category_id` int(11) DEFAULT NULL,
  `formation_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `educations`
--

INSERT INTO `educations` (`id`, `title`, `description`, `competences`, `difficulte`, `duree`, `prerequis`, `categorie`, `lien_ressources`, `impact_social`, `created_at`, `updated_at`, `category_id`, `formation_id`, `parent_id`) VALUES
(19, 'educariohn', 'edfezfezfezfezfezfz', 'github', 'Avancé', 44, '', 'bla', '', '', '2025-11-27 19:50:44', '2025-12-02 20:40:18', 20, 17, 29),
(20, 'cdcdcd', 'cdcdcdcdcdcdcd', 'python', 'Intermédiaire', 77, 'cdcdcdcdcd', 'cdcdcd', '', '', '2025-12-01 08:34:06', '2025-12-01 21:47:55', 22, 18, 26),
(26, 'test', 'testststststssqqst', 'github', 'Débutant', 88, '', 'data save', '', '', '2025-12-01 21:45:58', '2025-12-01 21:45:58', 13, 18, NULL),
(27, 'miaouuu', 'mioauauaua', 'ezfezf', 'Avancé', 44, 'bbbb', 'bbbbbbbbbbb', '', '', '2025-12-01 21:48:25', '2025-12-01 21:49:47', 27, 18, 20),
(29, 'hamrpunimmmm', 'aefaefae;lfalm,fkaefksoqslkqsnklfqsk', 'ezbz', 'Intermédiaire', 4450, 'wwcrg', 'rafff', '', '', '2025-12-02 20:38:06', '2025-12-02 20:40:18', 28, 17, 30),
(30, 'ouiweeeee', 'fffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', 'vcgh', 'Débutant', 14, 'bez', 'bleza', '', '', '2025-12-02 20:38:44', '2025-12-02 20:38:44', 29, 17, NULL),
(31, 'pdo ', 'we will learn the basics about pdo which is php data object ', 'programming', 'Débutant', 10, '', 'programming', '', '', '2025-12-02 20:58:25', '2025-12-10 12:49:56', 15, 22, 33),
(32, 'dgbgb', 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbb', 'nn', 'Intermédiaire', 54, '', 'bb', '', '', '2025-12-02 22:17:09', '2025-12-02 22:17:09', 18, 21, NULL),
(33, 'pdo', 'pdo formation for all the inforamtion ', 'php', 'Débutant', 22, 'pdoooooooooooooo', 'pdo', 'https://esttt', '', '2025-12-10 12:42:28', '2025-12-10 12:42:28', 30, 22, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `evenement`
--

CREATE TABLE `evenement` (
  `idevent` int(11) NOT NULL,
  `nom_evenet` varchar(255) NOT NULL,
  `dateevent` date NOT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  `duree` time NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `evenement`
--

INSERT INTO `evenement` (`idevent`, `nom_evenet`, `dateevent`, `date_debut`, `date_fin`, `duree`, `description`, `image`) VALUES
(1, 'bal3a', '2025-12-15', '2025-12-15 00:00:00', '2025-12-15 20:45:00', '20:45:00', '', 'public/uploads/events/event_693c87204fe75.jpg'),
(2, 'ga3da', '0000-00-00', '2026-01-01 10:00:00', '2026-02-02 11:11:00', '00:00:00', '', 'public/uploads/events/event_693c875a1e7c8.jpg'),
(3, 'tab7ira', '0000-00-00', '2025-12-12 22:13:00', '2025-12-13 00:00:00', '00:00:00', '', 'public/uploads/events/event_693c86e20f4ff.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `formation_id` int(11) DEFAULT NULL,
  `education_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `formation_id`, `education_id`, `created_at`) VALUES
(45, 2147483647, 19, NULL, '2025-12-01 19:54:22'),
(47, 2147483647, NULL, 20, '2025-12-01 19:54:41'),
(50, 2147483647, 18, NULL, '2025-12-01 19:56:31'),
(58, 1553802446, 19, NULL, '2025-12-01 21:42:01'),
(59, 1553802446, NULL, 19, '2025-12-01 21:50:52'),
(60, 1553802446, NULL, 20, '2025-12-01 21:51:00'),
(61, 1553802446, 17, NULL, '2025-12-01 21:51:15'),
(62, 1553802446, 21, NULL, '2025-12-02 13:45:19'),
(63, 856640820, 22, NULL, '2025-12-02 21:49:26'),
(64, 856640820, NULL, 31, '2025-12-02 21:49:39'),
(65, 856640820, 21, NULL, '2025-12-02 21:54:45'),
(66, 856640820, 13, NULL, '2025-12-02 22:22:36'),
(67, 727099744, 19, NULL, '2025-12-03 15:23:35'),
(68, 727099744, NULL, 31, '2025-12-03 15:23:49'),
(69, 1, 18, NULL, '2025-12-05 19:49:16');

-- --------------------------------------------------------

--
-- Table structure for table `formations`
--

CREATE TABLE `formations` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `competences` text DEFAULT NULL,
  `difficulte` enum('Débutant','Intermédiaire','Avancé') DEFAULT 'Débutant',
  `duree` int(11) DEFAULT 0,
  `categorie` varchar(255) DEFAULT NULL,
  `lien_ressources` varchar(500) DEFAULT NULL,
  `impact_social` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `formations`
--

INSERT INTO `formations` (`id`, `title`, `description`, `competences`, `difficulte`, `duree`, `categorie`, `lien_ressources`, `impact_social`, `created_at`, `updated_at`, `category_id`) VALUES
(13, 'ezfezze', 'fzfzefezezfez', '', 'Débutant', 21, 'test', 'http:test', '', '2025-11-24 20:57:14', '2025-11-24 20:57:14', 12),
(14, 'abcc', 'azdazdazezfzfez', 'c++', 'Débutant', 8, 'ibrahim', 'http:test', '', '2025-11-25 12:26:55', '2025-11-25 12:26:55', 14),
(16, 'uhiuhui', 'jhihjhjkhjkhjkhjk', 'github', 'Débutant', 5, 'graphic', 'http:test', '', '2025-11-25 13:51:30', '2025-11-25 13:51:30', 16),
(17, 'hamrouni', 'hamrouniiiiiiiiiiiiiiiiiiiiiiiiiiiii', 'c#', 'Débutant', 5, 'ee', 'http:test', '', '2025-11-27 19:49:20', '2025-11-27 19:49:20', 19),
(18, 'cdcdcdcd', 'cdcdcdcdcdcdcdcdcdcd', 'ez', 'Avancé', 88, 'ez', 'http:test', '', '2025-12-01 08:33:14', '2025-12-01 08:33:14', 21),
(19, 'khalil', 'khalillllllllllllllllllllllllllllllllll', 'khalilll', 'Débutant', 22, 'khalil', 'http:test', '', '2025-12-01 19:36:55', '2025-12-01 19:36:55', 23),
(21, '2a5', '2a55555555555555', 'c++', 'Débutant', 50, 'bb', 'http:test', '', '2025-12-02 12:48:54', '2025-12-02 12:48:54', 18),
(22, 'php', 'we will learn php toegther step by step ', 'php', 'Débutant', 400, 'programming', 'http:test', '', '2025-12-02 20:57:37', '2025-12-09 14:16:16', 15);

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT 'NULL = added by admin, otherwise user ID',
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `impact_social` varchar(50) DEFAULT NULL,
  `status` enum('development','published','archived') DEFAULT 'development',
  `approval_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `image_url` varchar(255) DEFAULT NULL,
  `demo_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`id`, `user_id`, `name`, `description`, `impact_social`, `status`, `approval_status`, `image_url`, `demo_url`, `created_at`, `updated_at`, `category_id`) VALUES
(1, 4, 'testbbbbbb', 'testtttttttttttttttt', 'testttt', 'published', 'approved', '/game-masters/public/assets/img/games/game_testbbbbbb_69333713d00d0.png', 'https://www.youtube.com/watch?v=szAKwfohv10&list=RDDGCN-s33Xmc&index=2', '2025-12-05 18:32:02', '2025-12-05 19:48:35', NULL),
(2, NULL, 'aaaaaaaaaaaaaaaaaaaaaaaab', 'bbbbbbbbbbbbbbbbbbbbb', 'bbbbbbbbbb', 'published', 'approved', '/game-masters/public/assets/img/games/game_aaaaaaaaaaaaaaaaaaaaaaaab_693336f75e3cd.jpg', 'https://www.youtube.com/watch?v=DGCN-s33Xmc&list=RDDGCN-s33Xmc&index=1', '2025-12-05 19:35:22', '2025-12-05 19:48:07', NULL),
(3, 4, 'other test', 'zezfezfezfezfezfze', 'testttt', 'published', 'approved', '/game-masters/public/assets/img/games/game_other_test_69333efe47bff.png', '', '2025-12-05 20:22:22', '2025-12-05 21:06:53', 3),
(5, NULL, 'jeux', 'jeuxxxxxxxxxxxxxxxxxxxxxxx', 'jeuxxx', 'published', 'approved', '/game-masters/public/assets/img/games/game_jeux_693340a945b89.png', 'https://www.youtube.com/watch?v=Vajga8uR2MQ', '2025-12-05 20:29:29', '2025-12-05 20:29:49', 3),
(6, 5, 'csgo', 'this game is for nurds ', 'becoming mentally fucked', 'published', 'approved', '/game-masters/public/assets/img/games/game_csgo_6934b7cf9b16b.jpg', 'https://www.youtube.com/watch?v=F74qK2UBybc', '2025-12-06 23:10:07', '2025-12-06 23:10:45', 1),
(7, 5, 'valorant', 'hamza s3idane z450', 'test hamzaaa', 'published', 'approved', '/game-masters/public/assets/img/games/game_valorant_693602f19dbaf.jpg', 'https://www.youtube.com/watch?v=C4EaUn-qTpg', '2025-12-07 20:30:02', '2025-12-08 21:51:44', 4),
(8, NULL, 'lol', 'riot gamessssssssss', 'league of legend', 'published', 'approved', '/game-masters/public/assets/img/games/game_lol_6936029d729c3.jpg', 'https://www.youtube.com/watch?v=crwcXwFUJy8', '2025-12-07 22:41:33', '2025-12-07 22:41:33', 1),
(9, 4, 'rocket league ', 'rocket league for the win ', 'rocket league ', 'published', 'approved', '/game-masters/public/assets/img/games/game_rocket_league__693747c27069a.jpg', 'https://www.youtube.com/watch?v=5C8sjxKKvvg', '2025-12-08 21:48:50', '2025-12-08 21:51:50', 2),
(10, 8, 'rocket league ', 'rocket leagueeeeeeeeee', 'rocket league for the win', 'published', 'approved', '/game-masters/public/assets/img/games/game_rocket_league__693748155e75b.jpg', 'https://www.youtube.com/watch?v=5C8sjxKKvvg', '2025-12-08 21:50:13', '2025-12-08 21:51:10', 2),
(11, 7, 'gta 5', 'gtaaaaaaaaaa', 'gta eazz', 'published', 'approved', '/game-masters/public/assets/img/games/game_gta_5_69374a5b5e32b.jpg', 'https://www.youtube.com/watch?v=aNub95OAKYQ', '2025-12-08 21:59:55', '2025-12-08 22:00:18', 3),
(12, 6, 'testt', 'testttttttttttttttttttztzt', 'testtttt', 'published', 'approved', '/game-masters/public/assets/img/games/game_testt_693a787fd559c.png', 'https://www.youtube.com/watch?v=8dTTYflN_1o', '2025-12-11 07:53:35', '2025-12-11 07:54:03', 1);

-- --------------------------------------------------------

--
-- Table structure for table `games_library`
--

CREATE TABLE `games_library` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `game_url` text NOT NULL,
  `thumbnail_url` varchar(500) DEFAULT NULL,
  `category` varchar(100) DEFAULT 'educational',
  `difficulty` enum('easy','medium','hard') DEFAULT 'easy',
  `game_type` enum('iframe','link','embed') DEFAULT 'iframe',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `games_library`
--

INSERT INTO `games_library` (`id`, `title`, `description`, `game_url`, `thumbnail_url`, `category`, `difficulty`, `game_type`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Climate Challenge', 'Un jeu éducatif sur le changement climatique et l\'impact environnemental. Apprenez à faire des choix durables.', 'https://www.bbc.co.uk/sn/hottopics/climatechange/climate_challenge/', 'https://images.unsplash.com/photo-1611273426858-450d8e3c9fce?w=400&h=300&fit=crop', 'social_impact', 'medium', 'link', 1, '2025-12-11 10:40:23', '2025-12-11 10:48:53'),
(2, 'CodeCombat', 'Apprenez la programmation en jouant. Développez vos compétences en codage de manière ludique.', 'https://codecombat.com/', 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=400&h=300&fit=crop', 'educational', 'medium', 'link', 1, '2025-12-11 10:40:23', '2025-12-11 10:48:53'),
(3, 'Scratch - Créer des Jeux', 'Plateforme pour créer et jouer à des jeux éducatifs. Développez votre créativité en programmation visuelle.', 'https://scratch.mit.edu/', 'https://images.unsplash.com/photo-1511512578047-dfb367046420?w=400&h=300&fit=crop', 'educational', 'easy', 'link', 1, '2025-12-11 10:40:23', '2025-12-11 10:51:07'),
(4, 'Stop Disasters!', 'Jeu de simulation sur la prévention des catastrophes naturelles. Apprenez à protéger les communautés.', 'https://www.stopdisastersgame.org/', 'https://images.unsplash.com/photo-1583212292454-1fe6229603b7?w=400&h=300&fit=crop', 'social_impact', 'medium', 'link', 1, '2025-12-11 10:40:23', '2025-12-11 10:51:07'),
(5, 'Foldit - Puzzles Scientifiques', 'Résolvez des puzzles de protéines et contribuez à la recherche scientifique. Impact social réel.', 'https://fold.it/', 'https://images.unsplash.com/photo-1532619675605-1ede6c4ed2d4?w=400&h=300&fit=crop', 'social_impact', 'hard', 'link', 1, '2025-12-11 10:40:23', '2025-12-11 10:51:07'),
(6, 'Lightbot - Programmation', 'Jeu de puzzle pour apprendre les concepts de programmation de manière simple et amusante.', 'https://lightbot.com/flash.html', 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=400&h=300&fit=crop', 'educational', 'easy', 'link', 1, '2025-12-11 10:40:23', '2025-12-11 10:51:07'),
(7, 'Free Rice - Éducation', 'Répondez à des questions et aidez à nourrir les personnes dans le besoin. Impact social direct.', 'https://freerice.com/', 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=400&h=300&fit=crop', 'social_impact', 'easy', 'link', 1, '2025-12-11 10:40:23', '2025-12-11 10:51:07'),
(8, 'TypingClub - Dactylographie', 'Améliorez vos compétences en dactylographie tout en apprenant. Essentiel pour le développement.', 'https://www.typingclub.com/', 'https://images.unsplash.com/photo-1542831371-29b0f74f9713?w=400&h=300&fit=crop', 'educational', 'easy', 'link', 1, '2025-12-11 10:40:23', '2025-12-11 10:51:07'),
(9, 'Kahoot! - Quiz Éducatifs', 'Créez et jouez à des quiz éducatifs interactifs. Apprentissage collaboratif et amusant.', 'https://kahoot.com/', 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=400&h=300&fit=crop', 'educational', 'easy', 'link', 1, '2025-12-11 10:40:23', '2025-12-11 10:51:07'),
(10, 'Eco - Simulation Écologique', 'Jeu de simulation où vous gérez un écosystème. Apprenez l\'équilibre écologique.', 'https://www.play.eco/', 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=400&h=300&fit=crop', 'social_impact', 'hard', 'link', 1, '2025-12-11 10:40:23', '2025-12-11 10:51:07'),
(11, 'Climate Challenge', 'Un jeu éducatif sur le changement climatique et l\'impact environnemental. Apprenez à faire des choix durables.', 'https://www.bbc.co.uk/sn/hottopics/climatechange/climate_challenge/', 'https://images.unsplash.com/photo-1611273426858-450d8e3c9fce?w=400&h=300&fit=crop', 'social_impact', 'medium', 'link', 1, '2025-12-11 10:51:07', '2025-12-11 10:51:07'),
(12, 'CodeCombat', 'Apprenez la programmation en jouant. Développez vos compétences en codage de manière ludique.', 'https://codecombat.com/', 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=400&h=300&fit=crop', 'educational', 'medium', 'link', 1, '2025-12-11 10:51:07', '2025-12-11 10:51:07'),
(13, 'Scratch - Créer des Jeux', 'Plateforme pour créer et jouer à des jeux éducatifs. Développez votre créativité en programmation visuelle.', 'https://scratch.mit.edu/', 'https://images.unsplash.com/photo-1511512578047-dfb367046420?w=400&h=300&fit=crop', 'educational', 'easy', 'link', 1, '2025-12-11 10:51:07', '2025-12-11 10:51:07'),
(14, 'Stop Disasters!', 'Jeu de simulation sur la prévention des catastrophes naturelles. Apprenez à protéger les communautés.', 'https://www.stopdisastersgame.org/', 'https://images.unsplash.com/photo-1583212292454-1fe6229603b7?w=400&h=300&fit=crop', 'social_impact', 'medium', 'link', 1, '2025-12-11 10:51:07', '2025-12-11 10:51:07'),
(15, 'Foldit - Puzzles Scientifiques', 'Résolvez des puzzles de protéines et contribuez à la recherche scientifique. Impact social réel.', 'https://fold.it/', 'https://images.unsplash.com/photo-1532619675605-1ede6c4ed2d4?w=400&h=300&fit=crop', 'social_impact', 'hard', 'link', 1, '2025-12-11 10:51:07', '2025-12-11 10:51:07'),
(16, 'Lightbot - Programmation', 'Jeu de puzzle pour apprendre les concepts de programmation de manière simple et amusante.', 'https://lightbot.com/flash.html', 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=400&h=300&fit=crop', 'educational', 'easy', 'link', 1, '2025-12-11 10:51:07', '2025-12-11 10:51:07'),
(17, 'Free Rice - Éducation', 'Répondez à des questions et aidez à nourrir les personnes dans le besoin. Impact social direct.', 'https://freerice.com/', 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=400&h=300&fit=crop', 'social_impact', 'easy', 'link', 1, '2025-12-11 10:51:07', '2025-12-11 10:51:07'),
(18, 'TypingClub - Dactylographie', 'Améliorez vos compétences en dactylographie tout en apprenant. Essentiel pour le développement.', 'https://www.typingclub.com/', 'https://images.unsplash.com/photo-1542831371-29b0f74f9713?w=400&h=300&fit=crop', 'educational', 'easy', 'link', 1, '2025-12-11 10:51:07', '2025-12-11 10:51:07'),
(19, 'Kahoot! - Quiz Éducatifs', 'Créez et jouez à des quiz éducatifs interactifs. Apprentissage collaboratif et amusant.', 'https://kahoot.com/', 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=400&h=300&fit=crop', 'educational', 'easy', 'link', 1, '2025-12-11 10:51:07', '2025-12-11 10:51:07'),
(20, 'Eco - Simulation Écologique', 'Jeu de simulation où vous gérez un écosystème. Apprenez l\'équilibre écologique.', 'https://www.play.eco/', 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=400&h=300&fit=crop', 'social_impact', 'hard', 'link', 1, '2025-12-11 10:51:07', '2025-12-11 10:51:07');

-- --------------------------------------------------------

--
-- Table structure for table `game_categories`
--

CREATE TABLE `game_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `game_categories`
--

INSERT INTO `game_categories` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Santé Mentale', 'Jeux axés sur le bien-être psychologique', '2025-12-05 16:14:22'),
(2, 'Écologie', 'Jeux sur la protection de l\'environnement', '2025-12-05 16:14:22'),
(3, 'Éducation', 'Jeux éducatifs et formateurs', '2025-12-05 16:14:22'),
(4, 'Inclusion', 'Jeux promouvant la diversité et l\'inclusion', '2025-12-05 16:14:22');

-- --------------------------------------------------------

--
-- Table structure for table `game_ratings`
--

CREATE TABLE `game_ratings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `game_ratings`
--

INSERT INTO `game_ratings` (`id`, `user_id`, `game_id`, `rating`, `created_at`) VALUES
(1, 15, 6, 5, '2025-12-08 20:31:14'),
(2, 15, 3, 5, '2025-12-08 20:31:23'),
(3, 5, 6, 5, '2025-12-08 21:21:33'),
(4, 7, 8, 2, '2025-12-08 21:28:56');

-- --------------------------------------------------------

--
-- Table structure for table `participation`
--

CREATE TABLE `participation` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) DEFAULT '',
  `email` varchar(150) NOT NULL,
  `age` int(11) DEFAULT 0,
  `idevent` int(11) NOT NULL,
  `date_participation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `participation`
--

INSERT INTO `participation` (`id`, `nom`, `prenom`, `email`, `age`, `idevent`, `date_participation`) VALUES
(1, 'ahmed', '', 'ahmed@gmail.com', 0, 1, '2025-12-11 22:46:13'),
(2, 'mohamed', '', 'mohamed@gmail.com', 0, 1, '2025-12-11 23:20:42'),
(3, 'khalill', '', 'khalil@gmail.com', 0, 2, '2025-12-12 16:19:01'),
(6, 'ayoubb', '', 'Ayoub@gmail.com', 0, 2, '2025-12-12 22:14:25');

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE `profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `discord` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `nationality` varchar(50) DEFAULT NULL,
  `gender` enum('male','female','other','prefer_not_say') DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `career_level` varchar(50) DEFAULT NULL,
  `expertise` varchar(100) DEFAULT NULL,
  `tech_stack` varchar(100) DEFAULT NULL,
  `timezone` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profiles`
--

INSERT INTO `profiles` (`id`, `user_id`, `first_name`, `last_name`, `discord`, `country`, `nationality`, `gender`, `birth_date`, `career_level`, `expertise`, `tech_stack`, `timezone`, `created_at`, `updated_at`) VALUES
(1, 4, 'ibrahim', 'Hamrouni', '', 'tn', 'tn', 'male', '2003-09-06', 'junior', '', '', 'Europe/Paris', '2025-12-05 20:02:37', '2025-12-05 20:02:37'),
(2, 22, 'ez', 'ezzzzzz', '', 'tunisie', 'tunisienne', 'male', '2003-09-06', 'mid', '', '', 'Europe/Paris', '2025-12-11 23:25:04', '2025-12-11 23:25:04'),
(3, 23, 'abcde', 'abcde', '', 'tunisie', 'tunisienne', 'male', '2004-11-10', '', 'php', 'php', 'Europe/Paris', '2025-12-11 23:39:45', '2025-12-11 23:39:45'),
(5, 25, 'slmou', 'slmouu', '', 'tunisie', 'tunisienne', 'male', '1999-10-01', 'junior', '', 'php', 'Europe/Paris', '2025-12-12 15:51:34', '2025-12-12 15:51:34'),
(6, 26, 'ayoub', 'aaa', '', 'tunisie', 'tunisienne', 'female', '1989-02-02', 'junior', 'php', 'php', 'Europe/Paris', '2025-12-12 19:06:45', '2025-12-12 19:06:45');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT '',
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `title`, `category`, `image`, `description`, `created_at`) VALUES
(1, 'projet test', 'test', 'testttt', 'testttttttttttttttttttttttttt', '2025-12-11 20:35:06'),
(2, 'ez', 'ezzzz', 'public/uploads/projects/project_ez_693b325f51952.png', 'ezzzzzzzzzzz', '2025-12-11 21:06:39');

-- --------------------------------------------------------

--
-- Table structure for table `reclamations`
--

CREATE TABLE `reclamations` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `reponse` text DEFAULT NULL,
  `statut` enum('en_attente','traité') DEFAULT 'en_attente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reclamations`
--

INSERT INTO `reclamations` (`id`, `titre`, `description`, `image_path`, `user_id`, `reponse`, `statut`, `created_at`, `updated_at`) VALUES
(1, 'reclamation test', 'reclamationnnnnnnnnnnnnnnnnnnnnnnnnnnnn', NULL, 15, 'ahla bik  f site te3na ya jabri ,, ya3tek 3asfour. + ya3tek douda.', 'traité', '2025-12-12 07:41:30', '2025-12-12 08:03:34');

-- --------------------------------------------------------

--
-- Table structure for table `test_answers`
--

CREATE TABLE `test_answers` (
  `id` int(11) NOT NULL,
  `test_attempt_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `user_answer` enum('a','b','c','d') DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT 0,
  `answered_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `test_answers`
--

INSERT INTO `test_answers` (`id`, `test_attempt_id`, `question_id`, `user_answer`, `is_correct`, `answered_at`) VALUES
(1, 1, 3, 'd', 0, '2025-12-10 20:44:53'),
(6, 1, 8, 'c', 0, '2025-12-10 20:45:08'),
(7, 1, 2, 'b', 1, '2025-12-10 20:45:27'),
(9, 1, 7, 'a', 0, '2025-12-10 20:45:48'),
(10, 1, 6, 'b', 0, '2025-12-10 20:45:58'),
(11, 1, 1, 'd', 0, '2025-12-10 20:46:07'),
(12, 1, 5, 'd', 0, '2025-12-10 20:46:14'),
(13, 1, 10, 'c', 0, '2025-12-10 20:46:21'),
(14, 1, 4, 'b', 1, '2025-12-10 20:46:26'),
(15, 1, 9, 'b', 1, '2025-12-10 20:46:45'),
(16, 2, 1, 'd', 0, '2025-12-10 20:49:35'),
(19, 2, 6, 'b', 0, '2025-12-10 20:49:51'),
(20, 2, 10, 'b', 1, '2025-12-10 20:49:56'),
(21, 2, 9, 'c', 0, '2025-12-10 20:50:02'),
(23, 2, 7, 'b', 1, '2025-12-10 20:50:09'),
(25, 2, 8, 'c', 0, '2025-12-10 20:50:15'),
(26, 2, 3, 'd', 0, '2025-12-10 20:50:18'),
(27, 2, 2, 'b', 1, '2025-12-10 20:50:22'),
(28, 2, 4, 'b', 1, '2025-12-10 20:50:27'),
(29, 2, 5, 'b', 1, '2025-12-10 20:50:32'),
(40, 6, 6, 'd', 0, '2025-12-11 07:28:40'),
(41, 6, 5, 'a', 0, '2025-12-11 07:28:42'),
(42, 6, 3, 'b', 1, '2025-12-11 07:28:43'),
(43, 6, 10, 'a', 0, '2025-12-11 07:28:44'),
(44, 6, 2, 'b', 1, '2025-12-11 07:28:45'),
(45, 6, 7, 'b', 1, '2025-12-11 07:28:46'),
(46, 6, 4, 'b', 1, '2025-12-11 07:28:47'),
(47, 6, 8, 'a', 0, '2025-12-11 07:28:48'),
(48, 6, 9, 'a', 0, '2025-12-11 07:28:50'),
(49, 6, 1, 'd', 0, '2025-12-11 07:28:51'),
(60, 7, 5, 'b', 1, '2025-12-11 07:38:14'),
(63, 7, 7, 'b', 1, '2025-12-11 07:44:57'),
(64, 7, 1, 'a', 0, '2025-12-11 07:45:31'),
(65, 7, 8, 'a', 0, '2025-12-11 07:45:55'),
(66, 7, 9, 'b', 1, '2025-12-11 07:46:42'),
(67, 7, 10, 'b', 1, '2025-12-11 07:46:59'),
(68, 7, 6, 'b', 0, '2025-12-11 07:47:21'),
(76, 8, 1, 'b', 1, '2025-12-12 19:25:39'),
(78, 8, 10, 'b', 1, '2025-12-12 19:25:51'),
(79, 8, 2, 'b', 1, '2025-12-12 19:25:56'),
(82, 8, 6, 'c', 0, '2025-12-12 19:26:01'),
(83, 8, 7, 'a', 0, '2025-12-12 19:26:01'),
(84, 8, 9, 'd', 0, '2025-12-12 19:26:03'),
(85, 8, 3, 'a', 0, '2025-12-12 19:26:04'),
(86, 8, 5, 'd', 0, '2025-12-12 19:26:05'),
(87, 8, 4, 'b', 1, '2025-12-12 19:26:06'),
(88, 8, 8, 'a', 0, '2025-12-12 19:26:08');

-- --------------------------------------------------------

--
-- Table structure for table `test_approvals`
--

CREATE TABLE `test_approvals` (
  `id` int(11) NOT NULL,
  `test_attempt_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `test_approvals`
--

INSERT INTO `test_approvals` (`id`, `test_attempt_id`, `admin_id`, `status`, `admin_notes`, `created_at`, `updated_at`) VALUES
(2, 2, 15, 'pending', NULL, '2025-12-10 20:50:36', '2025-12-10 20:50:36'),
(3, 6, 15, 'approved', 'congratulations\r\n', '2025-12-11 07:28:54', '2025-12-11 07:32:53'),
(4, 7, 15, 'approved', 'bronzeee', '2025-12-11 07:50:27', '2025-12-11 08:40:04'),
(5, 8, 15, 'pending', NULL, '2025-12-12 19:26:10', '2025-12-12 19:26:10');

-- --------------------------------------------------------

--
-- Table structure for table `test_attempts`
--

CREATE TABLE `test_attempts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `test_request_id` int(11) NOT NULL,
  `score` decimal(5,2) DEFAULT 0.00 COMMENT 'Percentage score (0-100)',
  `total_questions` int(11) DEFAULT 0,
  `correct_answers` int(11) DEFAULT 0,
  `time_taken` int(11) DEFAULT 0 COMMENT 'Time taken in seconds',
  `time_limit` int(11) DEFAULT 1800 COMMENT 'Time limit in seconds (30 minutes)',
  `status` enum('in_progress','completed','expired') DEFAULT 'in_progress',
  `started_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `submitted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `test_attempts`
--

INSERT INTO `test_attempts` (`id`, `user_id`, `test_request_id`, `score`, `total_questions`, `correct_answers`, `time_taken`, `time_limit`, `status`, `started_at`, `submitted_at`) VALUES
(1, 8, 1, 30.00, 10, 3, 240, 1800, 'completed', '2025-12-10 20:42:50', '2025-12-10 20:46:50'),
(2, 8, 1, 50.00, 10, 5, 68, 1800, 'completed', '2025-12-10 20:49:28', '2025-12-10 20:50:36'),
(3, 8, 1, 0.00, 0, 0, 0, 1800, 'expired', '2025-12-10 20:54:31', NULL),
(4, 5, 2, 0.00, 0, 0, 0, 1800, 'expired', '2025-12-10 21:27:48', NULL),
(5, 5, 2, 0.00, 0, 0, 0, 1800, 'expired', '2025-12-11 07:21:11', NULL),
(6, 7, 3, 40.00, 10, 4, 17, 1800, 'completed', '2025-12-11 07:28:37', '2025-12-11 07:28:54'),
(7, 6, 4, 57.14, 7, 4, 735, 1800, 'completed', '2025-12-11 07:38:12', '2025-12-11 07:50:27'),
(8, 26, 5, 40.00, 10, 4, 46, 1800, 'completed', '2025-12-12 19:25:24', '2025-12-12 19:26:10'),
(9, 18, 6, 0.00, 0, 0, 0, 1800, 'expired', '2025-12-12 22:40:21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `test_questions`
--

CREATE TABLE `test_questions` (
  `id` int(11) NOT NULL,
  `question` text NOT NULL,
  `option_a` varchar(255) NOT NULL,
  `option_b` varchar(255) NOT NULL,
  `option_c` varchar(255) NOT NULL,
  `option_d` varchar(255) NOT NULL,
  `correct_answer` enum('a','b','c','d') NOT NULL,
  `explanation` text DEFAULT NULL COMMENT 'Explanation of the correct answer',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `test_questions`
--

INSERT INTO `test_questions` (`id`, `question`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_answer`, `explanation`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Quelle entreprise a développé le jeu \"Minecraft\" qui a un fort impact éducatif ?', 'Epic Games', 'Mojang Studios', 'Valve Corporation', 'Ubisoft', 'b', 'Minecraft a été développé par Mojang Studios (maintenant propriété de Microsoft) et est largement utilisé dans l\'éducation pour développer la créativité et les compétences en programmation.', 1, '2025-12-10 20:30:07', '2025-12-10 20:30:07'),
(2, 'Quel est l\'un des principaux avantages de l\'apprentissage du développement de jeux vidéo ?', 'Améliorer uniquement les compétences artistiques', 'Développer la pensée logique, la résolution de problèmes et la créativité', 'Ne nécessite aucune compétence technique', 'Limite la communication', 'b', 'Le développement de jeux combine la programmation, la logique, la créativité et le travail d\'équipe, développant des compétences multiples.', 1, '2025-12-10 20:30:07', '2025-12-10 20:30:07'),
(3, 'Quel jeu est connu pour son impact social positif en sensibilisant à l\'écologie ?', 'Call of Duty', 'Terra Nil', 'Grand Theft Auto', 'FIFA', 'b', 'Terra Nil est un jeu de stratégie qui enseigne la restauration écologique et la protection de l\'environnement.', 1, '2025-12-10 20:30:07', '2025-12-10 20:30:07'),
(4, 'Quelle compétence technique est essentielle pour développer des jeux vidéo ?', 'Cuisiner', 'Programmation/Codage', 'Peindre', 'Chanter', 'b', 'La programmation est fondamentale pour créer des jeux vidéo, permettant de définir les mécaniques, les interactions et la logique du jeu.', 1, '2025-12-10 20:30:07', '2025-12-10 20:30:07'),
(5, 'Quel est l\'impact social positif des jeux éducatifs sur la société ?', 'Aucun impact', 'Ils peuvent améliorer l\'apprentissage et développer des compétences cognitives', 'Ils encouragent uniquement la compétition', 'Ils isolent les joueurs', 'b', 'Les jeux éducatifs peuvent rendre l\'apprentissage plus engageant, améliorer la rétention des connaissances et développer des compétences essentielles.', 1, '2025-12-10 20:30:07', '2025-12-10 20:30:07'),
(6, 'Quelle plateforme est souvent utilisée pour apprendre le développement de jeux débutants ?', 'Unity', 'Microsoft Word', 'Adobe Photoshop', 'Excel', 'a', 'Unity est un moteur de jeu populaire utilisé pour l\'apprentissage du développement de jeux, particulièrement pour les débutants.', 1, '2025-12-10 20:30:07', '2025-12-10 20:30:07'),
(7, 'Quel type de jeu peut avoir le plus grand impact social positif ?', 'Jeux violents uniquement', 'Jeux éducatifs et jeux avec messages sociaux', 'Jeux de casino', 'Aucun jeu', 'b', 'Les jeux éducatifs et ceux avec des messages sociaux positifs peuvent sensibiliser, éduquer et inspirer le changement social.', 1, '2025-12-10 20:30:07', '2025-12-10 20:30:07'),
(8, 'Quelle entreprise a développé \"Kind Words\", un jeu axé sur l\'empathie et le bien-être mental ?', 'Electronic Arts', 'Popcannibal', 'Activision', 'Nintendo', 'b', 'Kind Words a été développé par Popcannibal et se concentre sur l\'échange de messages positifs et le soutien émotionnel entre joueurs.', 1, '2025-12-10 20:30:07', '2025-12-10 20:30:07'),
(9, 'Pourquoi est-il important d\'apprendre le codage dans le contexte du développement de jeux ?', 'C\'est inutile', 'Cela permet de créer des mécaniques de jeu, des interactions et des systèmes complexes', 'Seuls les graphistes sont nécessaires', 'Les jeux n\'ont pas besoin de code', 'b', 'Le codage est essentiel pour créer la logique du jeu, les interactions, l\'IA, les systèmes de score et toutes les mécaniques de jeu.', 1, '2025-12-10 20:30:07', '2025-12-10 20:30:07'),
(10, 'Quel est un avantage du développement de jeux pour les jeunes apprenants ?', 'Limite la créativité', 'Développe la patience, la persévérance et les compétences en résolution de problèmes', 'Décourage la collaboration', 'Ne nécessite aucune pensée', 'b', 'Le développement de jeux enseigne la patience face aux bugs, la persévérance pour résoudre des problèmes complexes et encourage le travail collaboratif.', 1, '2025-12-10 20:30:07', '2025-12-10 20:30:07');

-- --------------------------------------------------------

--
-- Table structure for table `test_requests`
--

CREATE TABLE `test_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `motivational_letter` text NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `admin_response` text DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL COMMENT 'Admin who reviewed the request',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `test_requests`
--

INSERT INTO `test_requests` (`id`, `user_id`, `motivational_letter`, `status`, `admin_response`, `admin_id`, `created_at`, `updated_at`) VALUES
(1, 8, 'edezabdajizbdjazdbnjkazbdkazbdazjkbdazkdbazkjbdazkjdbazjkdbazjk', 'approved', 'you are welcome ', 15, '2025-12-10 20:36:54', '2025-12-10 21:06:04'),
(2, 5, 'zfbbjiabdjsbqjkbsqjkdbqsjkdbqsjkdbqkdqsjkbdqsjkbdqsjkbdqsjkbd', 'approved', 'slmou 3alaykom', 15, '2025-12-10 21:20:04', '2025-12-10 21:27:12'),
(3, 7, 'b bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb', 'approved', '', 15, '2025-12-11 07:27:59', '2025-12-11 07:28:19'),
(4, 6, 'n7b n3adi test za3 zhy hdhazdphaziodhazazdfazdazdazdaz', 'approved', '', 15, '2025-12-11 07:37:04', '2025-12-11 07:37:44'),
(5, 26, 'n7b na3adi test ya mr rrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrr', 'approved', '', 15, '2025-12-12 19:23:40', '2025-12-12 19:24:54'),
(6, 18, 'azdzadazdazdazdazdazdazdazdazdazdazdazdazdazdazdazdazda', 'approved', '', 15, '2025-12-12 22:39:27', '2025-12-12 22:39:56');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `verification_code` varchar(6) DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `role` enum('player','moderator','admin') DEFAULT 'player',
  `status` enum('active','inactive','banned') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `face_descriptor` text DEFAULT NULL COMMENT 'Descripteur facial encodé en JSON',
  `face_registered_at` timestamp NULL DEFAULT NULL COMMENT 'Date d''enregistrement du visage',
  `face_enabled` tinyint(1) DEFAULT 0 COMMENT 'Activer/désactiver la connexion par visage',
  `reset_code` varchar(64) DEFAULT NULL,
  `reset_code_expires` datetime DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `medal` enum('none','bronze','silver','gold') DEFAULT 'none',
  `medal_notification_seen` tinyint(1) DEFAULT 1,
  `banned_until` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `verification_code`, `email_verified`, `role`, `status`, `created_at`, `updated_at`, `face_descriptor`, `face_registered_at`, `face_enabled`, `reset_code`, `reset_code_expires`, `avatar`, `medal`, `medal_notification_seen`, `banned_until`) VALUES
(4, 'ibrahim_Hamrouniii', 'test@gmail.com', '$2y$10$V7RydaNQ4i36BC6j.Le3AegSL690/9Me4eGAY5bSbw1WNclHiHmm6', NULL, 1, 'moderator', 'active', '2025-12-05 17:18:24', '2025-12-08 21:51:50', NULL, NULL, 0, NULL, NULL, '/projet01/public/uploads/avatars/default_693313e0b22b6.svg', 'bronze', 1, NULL),
(5, 'khalil', 'khalil@gmail.com', '$2y$10$vsj2JhkL7cWlypCLnS.hNefGaiQ5u/iq7JAkBahQ92ZWhR8EaFSsK', NULL, 1, 'player', 'active', '2025-12-05 20:38:50', '2025-12-12 14:17:20', NULL, NULL, 0, NULL, NULL, '/projet01/public/uploads/avatars/default_693342da14403.svg', 'gold', 1, NULL),
(6, 'mohamed', 'mohamed@gmail.com', '$2y$10$JnV4RHYJdRPv80i6G.mAB.88P0mbaIDFLS5ENaJkQrEH7u5dB6BOS', NULL, 1, 'player', 'active', '2025-12-05 20:46:48', '2025-12-11 08:40:19', NULL, NULL, 0, NULL, NULL, 'public/assets/img/avatars/avatar18.jpg', 'bronze', 1, NULL),
(7, 'mariem', 'mariem@gmail.com', '$2y$10$rCGniV9XXHkeSqnGqRdKc.ZO63ZLDcv4xZKfu9nTuXnI02textvha', NULL, 1, 'player', 'active', '2025-12-05 20:54:08', '2025-12-11 22:56:33', NULL, NULL, 0, NULL, NULL, 'public/assets/img/avatars/avatar1.jpg', 'silver', 1, NULL),
(8, 'ahmed', 'ahmed@gmail.com', '$2y$10$/.xx6EmzcVF3uxOa4GGPVOqfXqjxHQ8mTpjBxJzzJVHV5jV0EbbSy', NULL, 1, 'player', 'active', '2025-12-05 21:32:12', '2025-12-12 13:15:55', NULL, NULL, 0, NULL, NULL, 'public/uploads/avatars/default_69334f5be3187.svg', 'gold', 1, NULL),
(15, 'ibrahim', 'admin@gamemasters.com', '$2y$10$ZLbuyFxPLlhIwe1eFK.OAuoGpOgtZ3Sb9ocb2HTFMItjpwp2oZV4O', NULL, 1, 'admin', 'active', '2025-12-06 19:28:39', '2025-12-12 18:53:39', '[-0.13592414557933807,0.11721291393041611,0.11298204958438873,-0.04190492630004883,-0.002827626885846257,-0.040400613099336624,0.04298543184995651,-0.08600135147571564,0.208157017827034,-0.12365482747554779,0.20581652224063873,-0.03489542379975319,-0.2629234492778778,-0.01916007697582245,0.008299005217850208,0.05082273483276367,-0.08550850301980972,-0.17350763082504272,-0.0596231184899807,-0.06319727748632431,0.08218782395124435,-0.029642747715115547,-0.036895278841257095,0.05568841099739075,-0.1663583219051361,-0.32138484716415405,-0.07576560974121094,-0.08000873029232025,0.03102750889956951,-0.11757928878068924,0.04415453225374222,0.016007516533136368,-0.1927468627691269,-0.05921241641044617,-0.04039285331964493,0.07779707759618759,0.02487996220588684,-0.04101318120956421,0.208394855260849,-0.0708300992846489,-0.19826945662498474,-0.0054511199705302715,0.06914912909269333,0.26709383726119995,0.05657112970948219,0.03981525078415871,0.021330025047063828,-0.06080581247806549,0.06422267109155655,-0.21500563621520996,0.14525532722473145,0.11613384634256363,0.10286083817481995,0.056719087064266205,0.11006008088588715,-0.18813075125217438,0.005480166524648666,0.10168790072202682,-0.20858816802501678,0.1296377182006836,0.09619055688381195,-0.01952216774225235,-0.014605078846216202,-0.013899926096200943,0.1711163967847824,0.05248614773154259,-0.11922973394393921,-0.06492406874895096,0.11894329637289047,-0.1395440697669983,0.03713076561689377,0.1256689578294754,-0.08750703185796738,-0.19226175546646118,-0.23353013396263123,0.15187829732894897,0.490587443113327,0.150338277220726,-0.11999187618494034,0.019670803099870682,-0.07892721891403198,-0.06268531829118729,0.08129236847162247,0.028395989909768105,-0.102353535592556,-0.0015017701080068946,-0.07228383421897888,0.12665240466594696,0.17548055946826935,0.11465311050415039,-0.06602882593870163,0.17023824155330658,0.0010870626429095864,-0.038106150925159454,-0.019913610070943832,0.061923492699861526,-0.11920814216136932,-0.007122342009097338,-0.12171385437250137,-3.934720552933868e-6,0.05288822203874588,-0.05467792972922325,-0.03167320042848587,0.12092926353216171,-0.21382366120815277,0.10509839653968811,0.03083987347781658,-0.07905658334493637,0.002511096652597189,0.12381495535373688,-0.1368764042854309,-0.059076979756355286,0.10767800360918045,-0.2786129415035248,0.12774646282196045,0.1462324559688568,0.0487029142677784,0.1356470137834549,0.04270528629422188,0.03723405674099922,0.09677048772573471,-0.03256735950708389,-0.1370171159505844,-0.06558159738779068,0.09199926257133484,-0.014510143548250198,0.12957502901554108,0.0210409052670002]', '2025-12-12 18:53:39', 1, NULL, NULL, NULL, 'none', 1, NULL),
(16, 'userrrrr', 'userrr@gmail.com', '$2y$10$E1t67Xbl08BtJcDQt7sE5u1q9ca2MWdDWup5kceweNvr1fQAwe/oe', NULL, 1, 'player', 'active', '2025-12-11 18:19:53', '2025-12-11 18:32:29', NULL, NULL, 0, NULL, NULL, 'public/assets/img/avatars/avatar14.jpg', 'none', 1, NULL),
(17, 'hamza', 'hamza@gmail.com', '$2y$10$R3z88fHUSMRwaqgy4DKiBek2QqYrBZNaxgSbkb7MQEHCsM.NtLM4K', NULL, 1, 'player', 'active', '2025-12-11 18:51:19', '2025-12-11 18:51:57', NULL, NULL, 0, NULL, NULL, 'public/uploads/avatars/default_693b12a729096.svg', 'none', 1, NULL),
(18, 'syrine', 'syrine@gmail.com', '$2y$10$o3rJHDy8Q1ydF67gc60xqugA19G1k68xFCqQMur/JfakYuHsuY7pu', NULL, 1, 'player', 'active', '2025-12-11 18:54:35', '2025-12-12 16:03:40', '[-0.06258804351091385,0.0745658427476883,0.04958207905292511,-0.09223558753728867,-0.003518733661621809,0.008590816520154476,-0.024193212389945984,-0.12105890363454819,0.21158266067504883,-0.1668419986963272,0.21410009264945984,-0.05765599012374878,-0.23606917262077332,0.02251361310482025,-0.10342434793710709,0.23403459787368774,-0.14759249985218048,-0.1295093148946762,-0.050019945949316025,-0.08223113417625427,0.07700386643409729,0.013705259189009666,0.055285654962062836,0.14884893596172333,-0.13620668649673462,-0.3004377782344818,-0.0863594263792038,-0.12839451432228088,-0.07719177007675171,-0.07641226798295975,0.01611872762441635,0.02756509929895401,-0.15364842116832733,0.061450228095054626,0.017825836315751076,0.012546051293611526,-0.009822692722082138,-0.10843294113874435,0.22758644819259644,0.05400791019201279,-0.21410882472991943,0.02406361512839794,0.06390804052352905,0.25969618558883667,0.15830199420452118,0.006323633249849081,-0.006081148516386747,-0.10111138224601746,0.13275156915187836,-0.23426778614521027,-0.009379025548696518,0.1226075142621994,0.044395118951797485,0.04350605234503746,0.06553272157907486,-0.09298098832368851,0.04941942170262337,0.06316888332366943,-0.19119587540626526,-0.0089956633746624,-0.009948848746716976,-0.04771897941827774,-0.0524824857711792,-0.0997588112950325,0.22888433933258057,0.12294074147939682,-0.18759611248970032,-0.06750748306512833,0.14416059851646423,-0.1451997309923172,-0.03348657488822937,0.08830230683088303,-0.16646580398082733,-0.19392520189285278,-0.23093388974666595,-0.028845198452472687,0.475382536649704,0.1405469924211502,-0.16286566853523254,0.07237905263900757,-0.04902869090437889,-0.005614287685602903,0.03640046343207359,0.17739838361740112,-0.026735521852970123,0.03476656600832939,0.012287972494959831,0.037479788064956665,0.17104949057102203,-0.015632839873433113,-0.07885046303272247,0.23224730789661407,-0.053569912910461426,0.008620251901447773,-0.0024901137221604586,0.05040272697806358,-0.10677745193243027,-0.05704999715089798,-0.11882352083921432,-0.06458146870136261,-0.05623596906661987,0.020886166021227837,-0.028710033744573593,0.06216774880886078,-0.24618111550807953,0.1688612699508667,-0.004180009942501783,-0.07232683151960373,-0.004272039979696274,0.11114681512117386,-0.07989414036273956,0.0022096734028309584,0.10972479730844498,-0.26193767786026,0.12424322962760925,0.14830586314201355,0.030209477990865707,0.11742080748081207,0.0168328694999218,0.0653282031416893,-0.038041114807128906,-0.011168908327817917,-0.15923674404621124,-0.09686259925365448,0.09098528325557709,-0.038543857634067535,0.13073711097240448,-0.057014621794223785]', '2025-12-12 16:03:40', 1, NULL, NULL, 'public/uploads/avatars/default_693b136baa44a.svg', 'none', 1, NULL),
(19, 'las3ed', 'las3ed@gmail.com', '$2y$10$8mqcGZXZllBHMbxY6it7uewBfhW74wxtx3jLEwd81mcxCmqCh/lPy', NULL, 1, 'player', 'active', '2025-12-11 19:00:43', '2025-12-11 19:01:14', NULL, NULL, 0, NULL, NULL, 'public/uploads/avatars/default_693b14db9818c.svg', 'none', 1, NULL),
(20, 'nour', 'nour@gmail.com', '$2y$10$UCcL172gDLSzH3xHRbh41uYBLXup5xBEh/Q0fs3wvpgstTWnO07iC', NULL, 1, 'player', 'active', '2025-12-11 19:07:42', '2025-12-11 19:08:08', NULL, NULL, 0, NULL, NULL, 'public/assets/img/avatars/avatar9.jpg', 'none', 1, NULL),
(21, 'tayara', 'tayara@gmail.com', '$2y$10$TEI3I7h8CwOszHQylL9cAufID3HoF8vnNZnHVFLn/biNDn7cpek/C', NULL, 1, 'player', 'active', '2025-12-11 23:19:52', '2025-12-11 23:20:22', NULL, NULL, 0, NULL, NULL, 'public/uploads/avatars/default_693b51980dc26.svg', 'none', 1, NULL),
(22, '3amlazher', '3amlazher@gmail.com', '$2y$10$dPlUIo.mIzRnSiIIKDsm4.6I3RRvUOK7urDzSV7BiaR/6N9jHX8XO', NULL, 1, 'player', 'active', '2025-12-11 23:23:49', '2025-12-11 23:24:27', NULL, NULL, 0, NULL, NULL, 'public/uploads/avatars/default_693b528581b50.svg', 'none', 1, NULL),
(23, 'abcde', 'abcde@gmail.com', '$2y$10$6Ic5LSLD3vXkedsvbS7CsOv.a.893M2B2/Zf9UR9d444gF7QExbVe', NULL, 1, 'player', 'active', '2025-12-11 23:38:36', '2025-12-11 23:39:07', NULL, NULL, 0, NULL, NULL, 'public/uploads/avatars/default_693b55fc55023.svg', 'none', 1, NULL),
(25, 'slmou', 'slmou@gmail.com', '$2y$10$J1V0oQzSS3mCzGeIUl5gzOCKkZTXbc4g7V1pjbZrKIo81K0fT.wuK', NULL, 1, 'player', 'active', '2025-12-12 15:50:13', '2025-12-12 15:50:30', NULL, NULL, 0, NULL, NULL, 'public/uploads/avatars/default_693c39b5624f6.svg', 'none', 1, NULL),
(26, 'ayoub ', 'Ayoub@gmail.com', '$2y$10$I0EdMV.9omcU2A8rc7wf6O3Wk40yXyFyqnNAuISNT/zAiLMImeo5C', NULL, 1, 'player', 'active', '2025-12-12 19:05:19', '2025-12-12 19:27:54', NULL, NULL, 0, NULL, NULL, 'public/assets/img/avatars/avatar8.jpg', 'gold', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_favorite_games`
--

CREATE TABLE `user_favorite_games` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_donations_project` (`project_id`);

--
-- Indexes for table `educations`
--
ALTER TABLE `educations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_educations_category` (`category_id`),
  ADD KEY `idx_formation_id` (`formation_id`),
  ADD KEY `fk_educations_parent` (`parent_id`);

--
-- Indexes for table `evenement`
--
ALTER TABLE `evenement`
  ADD PRIMARY KEY (`idevent`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_formation` (`user_id`,`formation_id`),
  ADD UNIQUE KEY `unique_user_education` (`user_id`,`education_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_formation_id` (`formation_id`),
  ADD KEY `idx_education_id` (`education_id`),
  ADD KEY `idx_favorites_user_id` (`user_id`);

--
-- Indexes for table `formations`
--
ALTER TABLE `formations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_formations_category` (`category_id`);

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_approval_status` (`approval_status`),
  ADD KEY `idx_category_id` (`category_id`);

--
-- Indexes for table `games_library`
--
ALTER TABLE `games_library`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_difficulty` (`difficulty`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `game_categories`
--
ALTER TABLE `game_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `game_ratings`
--
ALTER TABLE `game_ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_game_rating` (`user_id`,`game_id`),
  ADD KEY `idx_game_id` (`game_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `participation`
--
ALTER TABLE `participation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_participation_evenement` (`idevent`);

--
-- Indexes for table `profiles`
--
ALTER TABLE `profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reclamations`
--
ALTER TABLE `reclamations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_statut` (`statut`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `test_answers`
--
ALTER TABLE `test_answers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_attempt_question` (`test_attempt_id`,`question_id`),
  ADD KEY `idx_test_attempt_id` (`test_attempt_id`),
  ADD KEY `idx_question_id` (`question_id`);

--
-- Indexes for table `test_approvals`
--
ALTER TABLE `test_approvals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_test_attempt` (`test_attempt_id`),
  ADD KEY `idx_admin_id` (`admin_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `test_attempts`
--
ALTER TABLE `test_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_test_request_id` (`test_request_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `test_questions`
--
ALTER TABLE `test_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `test_requests`
--
ALTER TABLE `test_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_pending` (`user_id`,`status`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_role` (`role`);

--
-- Indexes for table `user_favorite_games`
--
ALTER TABLE `user_favorite_games`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_favorite_game` (`user_id`,`game_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_game_id` (`game_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `educations`
--
ALTER TABLE `educations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `evenement`
--
ALTER TABLE `evenement`
  MODIFY `idevent` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `formations`
--
ALTER TABLE `formations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `games_library`
--
ALTER TABLE `games_library`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `game_categories`
--
ALTER TABLE `game_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `game_ratings`
--
ALTER TABLE `game_ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `participation`
--
ALTER TABLE `participation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `profiles`
--
ALTER TABLE `profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reclamations`
--
ALTER TABLE `reclamations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `test_answers`
--
ALTER TABLE `test_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `test_approvals`
--
ALTER TABLE `test_approvals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `test_attempts`
--
ALTER TABLE `test_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `test_questions`
--
ALTER TABLE `test_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `test_requests`
--
ALTER TABLE `test_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `user_favorite_games`
--
ALTER TABLE `user_favorite_games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `educations`
--
ALTER TABLE `educations`
  ADD CONSTRAINT `fk_educations_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_educations_formation` FOREIGN KEY (`formation_id`) REFERENCES `formations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_educations_parent` FOREIGN KEY (`parent_id`) REFERENCES `educations` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `fk_favorites_education` FOREIGN KEY (`education_id`) REFERENCES `educations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_favorites_formation` FOREIGN KEY (`formation_id`) REFERENCES `formations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `formations`
--
ALTER TABLE `formations`
  ADD CONSTRAINT `fk_formations_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `games`
--
ALTER TABLE `games`
  ADD CONSTRAINT `fk_games_category` FOREIGN KEY (`category_id`) REFERENCES `game_categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_games_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `game_ratings`
--
ALTER TABLE `game_ratings`
  ADD CONSTRAINT `fk_ratings_game` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ratings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `participation`
--
ALTER TABLE `participation`
  ADD CONSTRAINT `fk_participation_evenement` FOREIGN KEY (`idevent`) REFERENCES `evenement` (`idevent`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `profiles`
--
ALTER TABLE `profiles`
  ADD CONSTRAINT `fk_profiles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reclamations`
--
ALTER TABLE `reclamations`
  ADD CONSTRAINT `fk_reclamations_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `test_answers`
--
ALTER TABLE `test_answers`
  ADD CONSTRAINT `test_answers_ibfk_1` FOREIGN KEY (`test_attempt_id`) REFERENCES `test_attempts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `test_answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `test_questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `test_approvals`
--
ALTER TABLE `test_approvals`
  ADD CONSTRAINT `test_approvals_ibfk_1` FOREIGN KEY (`test_attempt_id`) REFERENCES `test_attempts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `test_approvals_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `test_attempts`
--
ALTER TABLE `test_attempts`
  ADD CONSTRAINT `test_attempts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `test_attempts_ibfk_2` FOREIGN KEY (`test_request_id`) REFERENCES `test_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `test_requests`
--
ALTER TABLE `test_requests`
  ADD CONSTRAINT `test_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `test_requests_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_favorite_games`
--
ALTER TABLE `user_favorite_games`
  ADD CONSTRAINT `fk_fav_games_game` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_fav_games_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
