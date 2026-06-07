<?php
require_once "models/Project.php";
require_once "models/Event.php";
require_once "models/Game.php";
require_once "models/Donation.php";
require_once "models/Formation.php";
require_once "models/Education.php";
require_once "config/database.php";

class UnifiedChatbotController {
    
    private $projectModel;
    private $eventModel;
    private $gameModel;
    private $donationModel;
    private $formationModel;
    private $educationModel;
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->projectModel = new Project($this->conn);
        $this->eventModel = new Event($this->conn);
        $this->gameModel = new Game($this->conn);
        $this->donationModel = new Donation($this->conn);
        $this->formationModel = new Formation();
        $this->educationModel = new Education();
    }

    public function handleRequest() {
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['message']) || empty($input['message'])) {
            echo json_encode(['error' => 'No message provided']);
            return;
        }

        try {
            $userMessage = $input['message'];
            $response = $this->getResponse($userMessage);
            
            echo json_encode([
                'success' => true,
                'response' => $response
            ]);
        } catch (Exception $e) {
            error_log("UnifiedChatbot error: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'An error occurred processing your request.'
            ]);
        }
    }

    public function getResponse($userMessage) {
        $message = strtolower(trim($userMessage));
        $originalMessage = $userMessage;
        
        error_log("UnifiedChatbot - User message: " . $userMessage);
        
        // Normalize message
        $userMessage = trim($userMessage);
        if (empty($userMessage)) {
            return "Veuillez poser une question ! 😊";
        }
        
        // 1. Basic Greetings
        if (preg_match('/\b(hello|hi|bonjour|salut|coucou|hey|yo)\b/i', $userMessage)) {
            return "Bonjour ! 👋 Je suis votre **Assistant IA Universel** ! 🤖\n\nJe peux vous aider avec :\n- 🎮 **Jeux** : Trouver des jeux, catégories, notes\n- 🌍 **Projets** : Informations sur les projets et donations\n- 📅 **Événements** : Dates, participations, descriptions\n- 📚 **Formations** : Informations sur les formations et éducations\n\nQue souhaitez-vous savoir ?";
        }
        
        // 2. Detect topic FIRST (before handling list questions) - more precise detection
        $isFormation = preg_match('/\b(formation|formations|éducation|education|éducations|educations|apprendre|étudier|cours|leçon)\b/i', $userMessage);
        $isGame = preg_match('/\b(jeu|game|jeux|games|note|rating|catégorie|category|jouer|play)\b/i', $userMessage);
        $isProject = preg_match('/\b(projet|project|donation|don|donner|dons)\b/i', $userMessage);
        // Improved event detection - check for event keywords more broadly
        $isEvent = preg_match('/\b(événement|event|évènement|événements|events|évènements|participation|participer|participe|participations|date|quand|début|démarre|commence|start|fin|finit|termine|end|durée|duration)\b/i', $userMessage);
        
        // 3. Handle explicit list questions for detected topic
        if ($isFormation && (preg_match('/\b(liste|quels?|quelles?|tous?|tout|combien|nombre|total|avoir|disponible|afficher|montrer|voir)\b/i', $userMessage))) {
            return $this->getAllFormationsResponse();
        }
        
        if ($isGame && (preg_match('/\b(liste|quels?|quelles?|tous?|tout|combien|nombre|total|avoir|disponible|afficher|montrer|voir)\b/i', $userMessage))) {
            return $this->getAllGamesResponse();
        }
        
        if ($isProject && (preg_match('/\b(liste|quels?|quelles?|tous?|tout|combien|nombre|total|avoir|disponible|afficher|montrer|voir)\b/i', $userMessage))) {
            return $this->getAllProjectsResponse();
        }
        
        if ($isEvent && (preg_match('/\b(liste|quels?|quelles?|tous?|tout|combien|nombre|total|avoir|disponible|afficher|montrer|voir)\b/i', $userMessage))) {
            return $this->getAllEventsResponse();
        }
        
        // 4. Route to appropriate handler based on detected topic (ONLY search in that topic)
        if ($isFormation) {
            error_log("UnifiedChatbot - Routing to formations handler");
            return $this->handleFormationQuestion($userMessage, $originalMessage);
        } elseif ($isGame) {
            error_log("UnifiedChatbot - Routing to games handler");
            return $this->handleGameQuestion($userMessage, $originalMessage);
        } elseif ($isProject) {
            error_log("UnifiedChatbot - Routing to projects handler");
            return $this->handleProjectQuestion($userMessage, $originalMessage);
        } elseif ($isEvent) {
            error_log("UnifiedChatbot - Routing to events handler");
            return $this->handleEventQuestion($userMessage, $originalMessage);
        }
        
        // 5. General list question without specific topic
        if (preg_match('/\b(quels?|quelles?|liste|tous?|tout|combien|nombre|total|avoir|disponible|afficher|montrer|voir)\b/i', $userMessage)) {
            return $this->handleGeneralListQuestion($userMessage);
        }
        
        // 6. Default response with suggestions
        return "Je peux vous aider avec plusieurs sujets ! 🤖\n\nEssayez de me demander :\n- 🎮 \"Liste des jeux\" ou \"Quels jeux avez-vous ?\"\n- 🌍 \"Liste des projets\" ou \"Statistiques donations\"\n- 📅 \"Liste des événements\" ou \"Quand commence...\"\n- 📚 \"Formations disponibles\" ou \"Liste des formations\"\n\nOu posez-moi une question spécifique !";
    }
    
    // Project handlers (from ProjectChatbotController)
    private function handleProjectQuestion($userMessage, $originalMessage) {
        // Questions about "what projects do you have"
        if (preg_match('/\b(quels|quelles|liste|tous|tout|combien|nombre|total)\b.*\b(projets|projects|projet|project)\b/i', $userMessage) || 
            preg_match('/\b(projets|projects|projet|project)\b.*\b(quels|quelles|liste|tous|tout|combien|nombre|total|avez|disponible|avoir)\b/i', $userMessage)) {
            return $this->getAllProjectsResponse();
        }

        // Questions about donations
        if (preg_match('/\b(donation|donations|don|dons)\b/i', $userMessage)) {
            return $this->handleDonationQuestion($userMessage);
        }

        // Extract keywords and search projects
        $stopWords = ['je', 'tu', 'il', 'elle', 'nous', 'vous', 'ils', 'elles', 'le', 'la', 'les', 'un', 'une', 'des', 'de', 'du', 'au', 'aux', 'et', 'ou', 'mais', 'donc', 'or', 'ni', 'car', 'ce', 'cet', 'cette', 'ces', 'mon', 'ton', 'son', 'ma', 'ta', 'sa', 'mes', 'tes', 'ses', 'notre', 'votre', 'leur', 'nos', 'vos', 'leurs', 'qui', 'que', 'quoi', 'dont', 'où', 'quand', 'comment', 'pourquoi', 'quel', 'quelle', 'quels', 'quelles', 'est', 'sont', 'a', 'ont', 'veux', 'voudrais', 'souhaite', 'aimerais', 'apprendre', 'chercher', 'trouver', 'avoir', 'besoin', 'aide', 'sur', 'dans', 'par', 'pour', 'vers', 'avec', 'sans', 'sous', 'projet', 'projets', 'project', 'projects', 'les', 'des', 'tous', 'tout', 'toute', 'toutes'];
        
        $cleanMessage = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $userMessage);
        $words = explode(' ', strtolower($cleanMessage));
        
        $keywords = [];
        foreach ($words as $word) {
            $word = trim($word);
            if (!empty($word) && !in_array($word, $stopWords) && strlen($word) > 2) {
                $keywords[] = $word;
            }
        }

        if (empty($keywords)) {
            $keywords = [preg_replace('/[^\p{L}\p{N}]/u', '', $userMessage)];
        }

        try {
            $projects = $this->projectModel->getAllProjects();
        } catch (Exception $e) {
            error_log("Error fetching projects: " . $e->getMessage());
            $projects = [];
        }

        $foundItems = [];
        $searchString = implode(' ', $keywords);
        
        foreach ($projects as $project) {
            $score = 0;
            $title = strtolower($project['title'] ?? '');
            $description = strtolower($project['description'] ?? '');
            $category = strtolower($project['category'] ?? '');
            
            if ($title === $searchString) {
                $score += 100;
            } elseif (strpos($title, $searchString) !== false || strpos($searchString, $title) !== false) {
                $score += 50;
            }
            
            foreach ($keywords as $keyword) {
                if (strpos($title, $keyword) !== false) $score += 15;
                if (strpos($description, $keyword) !== false) $score += 5;
                if (strpos($category, $keyword) !== false) $score += 10;
            }
            
            if ($score >= 5) {
                $project['score'] = $score;
                $foundItems[] = $project;
            }
        }

        usort($foundItems, function($a, $b) {
            return $b['score'] - $a['score'];
        });

        if (!empty($foundItems)) {
            $totalCount = count($foundItems);
            
            if ($totalCount === 1 && $foundItems[0]['score'] >= 10) {
                return $this->getDetailedProjectInfo($foundItems[0]);
            }
            
            $response = "🌍 J'ai trouvé **" . $totalCount . " projet(s)** correspondant à votre recherche :\n\n";
            
            $count = 0;
            foreach ($foundItems as $project) {
                if ($count >= 5) break;
                
                $donationStats = $this->getProjectDonationStats($project['id']);
                
                $response .= "🌍 **" . htmlspecialchars($project['title'] ?? 'Sans nom') . "**\n";
                
                if (!empty($project['category'])) {
                    $response .= "📁 Catégorie : " . htmlspecialchars($project['category']) . "\n";
                }
                
                $response .= "💝 Donations : " . $donationStats['count'] . " don(s) - Total : " . number_format($donationStats['total'], 2, ',', ' ') . "€\n";
                
                $desc = !empty($project['description']) ? strip_tags($project['description']) : 'Aucune description disponible';
                $desc = mb_substr($desc, 0, 120, 'UTF-8');
                if (mb_strlen($project['description'] ?? '', 'UTF-8') > 120) $desc .= "...";
                $response .= "📝 " . $desc . "\n\n";
                
                $count++;
            }
            
            if ($totalCount > 5) {
                $response .= "... et " . ($totalCount - 5) . " autre(s) projet(s).\n\n";
            }
            
            $response .= "💡 Tapez le nom d'un projet pour plus de détails !";
            return $response;
        }

        return "Je n'ai pas trouvé de projet correspondant à votre recherche. 😔\n\nEssayez de :\n- 📝 Utiliser des mots-clés du nom ou de la description\n- 🔍 Voir tous les projets disponibles\n- 💡 Me demander \"Liste tous les projets\"";
    }
    
    // Event handlers (from EventChatbotController)
    private function handleEventQuestion($userMessage, $originalMessage) {
        error_log("UnifiedChatbot - handleEventQuestion called with: " . $userMessage);
        
        // Questions about "what events do you have"
        if (preg_match('/\b(quels|quelles|liste|tous|tout|combien|nombre|total)\b.*\b(événements|events|événement|event|évènements|évènement)\b/i', $userMessage) || 
            preg_match('/\b(événements|events|événement|event|évènements|évènement)\b.*\b(quels|quelles|liste|tous|tout|combien|nombre|total|avez|disponible|avoir)\b/i', $userMessage)) {
            error_log("UnifiedChatbot - Returning getAllEventsResponse");
            return $this->getAllEventsResponse();
        }

        // Questions about dates/times
        if (preg_match('/\b(quand|date|début|démarre|commence|start|fin|finit|termine|end|durée|duration)\b/i', $userMessage)) {
            return $this->handleDateQuestion($userMessage);
        }

        if (preg_match('/\b(participer|participation|inscrire|inscription|s\'inscrire|s\'inscrire)\b/i', $userMessage)) {
            return "👥 **Pour participer à un événement** :\n\n" .
                   "1. 📅 Consultez la liste des événements disponibles\n" .
                   "2. 🎯 Choisissez l'événement qui vous intéresse\n" .
                   "3. ➕ Cliquez sur le bouton \"Participer\"\n" .
                   "4. ✅ Votre participation sera enregistrée !\n\n" .
                   "💡 Vous devez être connecté pour participer à un événement.\n" .
                   "📋 Vous pouvez voir vos participations dans \"Mes Participations\".";
        }
        
        // Questions about specific events (e.g., "Tell me about [event name]", "What is [event name]?")
        if (preg_match('/\b(parle|dis|raconte|explique|décris|informe|donne|montre)\b.*\b(sur|de|à propos|au sujet)\b/i', $userMessage) ||
            preg_match('/\b(qu\'est|qu\'est-ce|what is|what\'s|c\'est quoi|qu\'est-ce que c\'est)\b/i', $userMessage) ||
            preg_match('/\b(à propos|au sujet|sur|de|informations|info|détails|détail)\b.*\b(événement|event|évènement)\b/i', $userMessage)) {
            return $this->handleSpecificEventQuestion($userMessage, $originalMessage);
        }

        // Search events
        $stopWords = ['je', 'tu', 'il', 'elle', 'nous', 'vous', 'ils', 'elles', 'le', 'la', 'les', 'un', 'une', 'des', 'de', 'du', 'au', 'aux', 'et', 'ou', 'mais', 'donc', 'or', 'ni', 'car', 'ce', 'cet', 'cette', 'ces', 'mon', 'ton', 'son', 'ma', 'ta', 'sa', 'mes', 'tes', 'ses', 'notre', 'votre', 'leur', 'nos', 'vos', 'leurs', 'qui', 'que', 'quoi', 'dont', 'où', 'quand', 'comment', 'pourquoi', 'quel', 'quelle', 'quels', 'quelles', 'est', 'sont', 'a', 'ont', 'veux', 'voudrais', 'souhaite', 'aimerais', 'apprendre', 'chercher', 'trouver', 'avoir', 'besoin', 'aide', 'sur', 'dans', 'par', 'pour', 'vers', 'avec', 'sans', 'sous', 'événement', 'événements', 'event', 'events', 'les', 'des', 'tous', 'tout', 'toute', 'toutes'];
        
        $cleanMessage = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $userMessage);
        $words = explode(' ', strtolower($cleanMessage));
        
        $keywords = [];
        foreach ($words as $word) {
            $word = trim($word);
            if (!empty($word) && !in_array($word, $stopWords) && strlen($word) > 2) {
                $keywords[] = $word;
            }
        }

        if (empty($keywords)) {
            $keywords = [preg_replace('/[^\p{L}\p{N}]/u', '', $userMessage)];
        }

        try {
            $events = $this->eventModel->getAllEvents();
        } catch (Exception $e) {
            error_log("Error fetching events: " . $e->getMessage());
            $events = [];
        }

        $foundItems = [];
        $searchString = implode(' ', $keywords);
        
        foreach ($events as $event) {
            $score = 0;
            $title = strtolower($event['nom_evenet'] ?? '');
            $description = strtolower($event['description'] ?? '');
            
            if ($title === $searchString) {
                $score += 100;
            } elseif (strpos($title, $searchString) !== false || strpos($searchString, $title) !== false) {
                $score += 50;
            }
            
            foreach ($keywords as $keyword) {
                if (strpos($title, $keyword) !== false) $score += 15;
                if (strpos($description, $keyword) !== false) $score += 5;
            }
            
            if ($score >= 5) {
                $event['score'] = $score;
                $foundItems[] = $event;
            }
        }

        usort($foundItems, function($a, $b) {
            return $b['score'] - $a['score'];
        });

        if (!empty($foundItems)) {
            $totalCount = count($foundItems);
            
            if ($totalCount === 1 && $foundItems[0]['score'] >= 10) {
                return $this->getDetailedEventInfo($foundItems[0]);
            }
            
            $response = "📅 J'ai trouvé **" . $totalCount . " événement(s)** correspondant à votre recherche :\n\n";
            
            $count = 0;
            foreach ($foundItems as $event) {
                if ($count >= 5) break;
                
                $eventId = $event['idevent'] ?? $event['id'] ?? 0;
                $dateInfo = $this->getEventDateInfo($event);
                
                $response .= "📅 **" . htmlspecialchars($event['nom_evenet'] ?? 'Sans nom') . "**\n";
                $response .= $dateInfo . "\n";
                
                $desc = !empty($event['description']) ? strip_tags($event['description']) : 'Aucune description disponible';
                $desc = mb_substr($desc, 0, 120, 'UTF-8');
                if (mb_strlen($event['description'] ?? '', 'UTF-8') > 120) $desc .= "...";
                $response .= "📝 " . $desc . "\n";
                
                // Add link to event page if available
                $response .= "🔗 [Voir plus →](?action=events)\n\n";
                
                $count++;
            }
            
            if ($totalCount > 5) {
                $response .= "... et " . ($totalCount - 5) . " autre(s) événement(s).\n\n";
            }
            
            $response .= "💡 Tapez le nom d'un événement pour plus de détails !";
            return $response;
        }

        return "Je n'ai pas trouvé d'événement correspondant à votre recherche. 😔\n\nEssayez de :\n- 📝 Utiliser des mots-clés du nom ou de la description\n- 🔍 Voir tous les événements disponibles\n- 💡 Me demander \"Liste tous les événements\"";
    }
    
    // Game handlers (from GameChatbotController)
    private function handleGameQuestion($userMessage, $originalMessage) {
        if (preg_match('/\b(quels|quelles|liste|tous|tout|combien|nombre|total)\b.*\b(jeux|games|jeu|game)\b/i', $userMessage) || 
            preg_match('/\b(jeux|games|jeu|game)\b.*\b(quels|quelles|liste|tous|tout|combien|nombre|total|avez|disponible|avoir)\b/i', $userMessage)) {
            return $this->getAllGamesResponse();
        }

        // Extract keywords and search games
        $stopWords = ['je', 'tu', 'il', 'elle', 'nous', 'vous', 'ils', 'elles', 'le', 'la', 'les', 'un', 'une', 'des', 'de', 'du', 'au', 'aux', 'et', 'ou', 'mais', 'donc', 'or', 'ni', 'car', 'ce', 'cet', 'cette', 'ces', 'mon', 'ton', 'son', 'ma', 'ta', 'sa', 'mes', 'tes', 'ses', 'notre', 'votre', 'leur', 'nos', 'vos', 'leurs', 'qui', 'que', 'quoi', 'dont', 'où', 'quand', 'comment', 'pourquoi', 'quel', 'quelle', 'quels', 'quelles', 'est', 'sont', 'a', 'ont', 'veux', 'voudrais', 'souhaite', 'aimerais', 'apprendre', 'chercher', 'trouver', 'avoir', 'besoin', 'aide', 'sur', 'dans', 'par', 'pour', 'vers', 'avec', 'sans', 'sous', 'jeu', 'jeux', 'game', 'games', 'les', 'des', 'tous', 'tout', 'toute', 'toutes'];
        
        $cleanMessage = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $userMessage);
        $words = explode(' ', strtolower($cleanMessage));
        
        $keywords = [];
        foreach ($words as $word) {
            $word = trim($word);
            if (!empty($word) && !in_array($word, $stopWords) && strlen($word) > 2) {
                $keywords[] = $word;
            }
        }

        if (empty($keywords)) {
            $keywords = [preg_replace('/[^\p{L}\p{N}]/u', '', $userMessage)];
        }

        try {
            $query = "SELECT g.*, gc.name as category_name,
                      (SELECT AVG(rating) FROM game_ratings WHERE game_id = g.id) as rating_average,
                      (SELECT COUNT(*) FROM game_ratings WHERE game_id = g.id) as rating_count
                      FROM games g 
                      LEFT JOIN game_categories gc ON g.category_id = gc.id 
                      WHERE g.status = 'published'";
            
            try {
                $checkQuery = $this->conn->query("SHOW COLUMNS FROM games LIKE 'approval_status'");
                if ($checkQuery->rowCount() > 0) {
                    $query .= " AND g.approval_status = 'approved'";
                }
            } catch (PDOException $e) {}
            
            $query .= " ORDER BY g.created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching games: " . $e->getMessage());
            $games = [];
        }

        $foundItems = [];
        $searchString = implode(' ', $keywords);
        
        foreach ($games as $game) {
            $score = 0;
            $name = strtolower($game['name'] ?? '');
            $description = strtolower($game['description'] ?? '');
            $categoryName = strtolower($game['category_name'] ?? '');
            
            if ($name === $searchString) {
                $score += 100;
            } elseif (strpos($name, $searchString) !== false || strpos($searchString, $name) !== false) {
                $score += 50;
            }
            
            foreach ($keywords as $keyword) {
                if (strpos($name, $keyword) !== false) $score += 15;
                if (strpos($description, $keyword) !== false) $score += 4;
                if (strpos($categoryName, $keyword) !== false) $score += 8;
            }
            
            if ($score >= 5) {
                $game['score'] = $score;
                $foundItems[] = $game;
            }
        }

        usort($foundItems, function($a, $b) {
            return $b['score'] - $a['score'];
        });

        if (!empty($foundItems)) {
            $totalCount = count($foundItems);
            
            if ($totalCount === 1 && $foundItems[0]['score'] >= 10) {
                return $this->getDetailedGameInfo($foundItems[0]);
            }
            
            $response = "🎮 J'ai trouvé **" . $totalCount . " jeu(x)** correspondant à votre recherche :\n\n";
            
            $count = 0;
            foreach ($foundItems as $game) {
                if ($count >= 5) break;
                
                $response .= "🎮 **" . htmlspecialchars($game['name'] ?? 'Sans nom') . "**\n";
                
                if (!empty($game['category_name'])) {
                    $response .= "📁 Catégorie : " . htmlspecialchars($game['category_name']) . "\n";
                }
                
                if (!empty($game['rating_average']) && $game['rating_average'] > 0) {
                    $rating = round($game['rating_average'], 1);
                    $countRatings = $game['rating_count'] ?? 0;
                    $response .= "⭐ Note : " . $rating . "/5 (" . $countRatings . " avis)\n";
                }
                
                $desc = !empty($game['description']) ? strip_tags($game['description']) : 'Aucune description disponible';
                $desc = mb_substr($desc, 0, 120, 'UTF-8');
                if (mb_strlen($game['description'] ?? '', 'UTF-8') > 120) $desc .= "...";
                $response .= "📝 " . $desc . "\n\n";
                
                $count++;
            }
            
            if ($totalCount > 5) {
                $response .= "... et " . ($totalCount - 5) . " autre(s) jeu(x).\n\n";
            }
            
            $response .= "💡 Tapez le nom d'un jeu pour plus de détails !";
            return $response;
        }

        return "Je n'ai pas trouvé de jeu correspondant à votre recherche. 😔\n\nEssayez de :\n- 📝 Utiliser des mots-clés du nom ou de la description\n- 🔍 Voir tous les jeux disponibles\n- 💡 Me demander \"Liste tous les jeux\"";
    }
    
    // Formation handlers
    private function handleFormationQuestion($userMessage, $originalMessage) {
        // Questions about "what formations do you have"
        if (preg_match('/\b(quels|quelles|liste|tous|tout|combien|nombre|total)\b.*\b(formations?|éducations?|educations?)\b/i', $userMessage) || 
            preg_match('/\b(formations?|éducations?|educations?)\b.*\b(quels|quelles|liste|tous|tout|combien|nombre|total|avez|disponible|avoir)\b/i', $userMessage)) {
            return $this->getAllFormationsResponse();
        }

        // Extract keywords and search
        $stopWords = ['je', 'tu', 'il', 'elle', 'nous', 'vous', 'ils', 'elles', 'le', 'la', 'les', 'un', 'une', 'des', 'de', 'du', 'au', 'aux', 'et', 'ou', 'mais', 'donc', 'or', 'ni', 'car', 'ce', 'cet', 'cette', 'ces', 'mon', 'ton', 'son', 'ma', 'ta', 'sa', 'mes', 'tes', 'ses', 'notre', 'votre', 'leur', 'nos', 'vos', 'leurs', 'qui', 'que', 'quoi', 'dont', 'où', 'quand', 'comment', 'pourquoi', 'quel', 'quelle', 'quels', 'quelles', 'est', 'sont', 'a', 'ont', 'veux', 'voudrais', 'souhaite', 'aimerais', 'apprendre', 'chercher', 'trouver', 'avoir', 'besoin', 'aide', 'sur', 'dans', 'par', 'pour', 'vers', 'avec', 'sans', 'sous', 'formation', 'formations', 'éducation', 'éducations', 'education', 'educations', 'les', 'des', 'tous', 'tout', 'toute', 'toutes'];
        
        $cleanMessage = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $userMessage);
        $words = explode(' ', strtolower($cleanMessage));
        
        $keywords = [];
        foreach ($words as $word) {
            $word = trim($word);
            if (!empty($word) && !in_array($word, $stopWords) && strlen($word) > 2) {
                $keywords[] = $word;
            }
        }

        if (empty($keywords)) {
            $keywords = [preg_replace('/[^\p{L}\p{N}]/u', '', $userMessage)];
        }

        try {
            // Search formations
            $formations = $this->formationModel->getAll()->fetchAll(PDO::FETCH_ASSOC);
            $educations = $this->educationModel->getAll()->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error fetching formations/educations: " . $e->getMessage());
            $formations = [];
            $educations = [];
        }

        $foundFormations = [];
        $foundEducations = [];
        $searchString = implode(' ', $keywords);
        
        // Search in formations
        foreach ($formations as $formation) {
            $score = 0;
            $title = strtolower($formation['title'] ?? '');
            $description = strtolower($formation['description'] ?? '');
            
            if ($title === $searchString) {
                $score += 100;
            } elseif (strpos($title, $searchString) !== false || strpos($searchString, $title) !== false) {
                $score += 50;
            }
            
            foreach ($keywords as $keyword) {
                if (strpos($title, $keyword) !== false) $score += 15;
                if (strpos($description, $keyword) !== false) $score += 5;
            }
            
            if ($score >= 5) {
                $formation['score'] = $score;
                $foundFormations[] = $formation;
            }
        }
        
        // Search in educations
        foreach ($educations as $education) {
            $score = 0;
            $title = strtolower($education['title'] ?? '');
            $description = strtolower($education['description'] ?? '');
            
            if ($title === $searchString) {
                $score += 100;
            } elseif (strpos($title, $searchString) !== false || strpos($searchString, $title) !== false) {
                $score += 50;
            }
            
            foreach ($keywords as $keyword) {
                if (strpos($title, $keyword) !== false) $score += 15;
                if (strpos($description, $keyword) !== false) $score += 5;
            }
            
            if ($score >= 5) {
                $education['score'] = $score;
                $foundEducations[] = $education;
            }
        }

        usort($foundFormations, function($a, $b) {
            return $b['score'] - $a['score'];
        });
        
        usort($foundEducations, function($a, $b) {
            return $b['score'] - $a['score'];
        });

        // Construct response
        $response = "";
        
        if (!empty($foundFormations)) {
            $response .= "📚 **Formations trouvées** (" . count($foundFormations) . ") :\n\n";
            
            $count = 0;
            foreach ($foundFormations as $formation) {
                if ($count >= 3) break;
                
                $formationId = $formation['id'] ?? 0;
                $formationTitle = htmlspecialchars($formation['title'] ?? 'Sans nom');
                
                $response .= "📖 **" . $formationTitle . "**\n";
                
                $desc = !empty($formation['description']) ? strip_tags($formation['description']) : 'Aucune description disponible';
                $desc = mb_substr($desc, 0, 100, 'UTF-8');
                if (mb_strlen($formation['description'] ?? '', 'UTF-8') > 100) $desc .= "...";
                $response .= "📝 " . $desc . "\n";
                
                // Add link to formation detail page
                if ($formationId > 0) {
                    $formationUrl = "?controller=formation&action=detail&id=" . $formationId;
                    $response .= "🔗 [Voir plus →](" . $formationUrl . ")\n\n";
                } else {
                    $response .= "\n";
                }
                
                $count++;
            }
        }
        
        if (!empty($foundEducations)) {
            $response .= "📝 **Éducations trouvées** (" . count($foundEducations) . ") :\n\n";
            
            $count = 0;
            foreach ($foundEducations as $education) {
                if ($count >= 3) break;
                
                $educationId = $education['id'] ?? 0;
                $educationTitle = htmlspecialchars($education['title'] ?? 'Sans nom');
                $formationId = $education['formation_id'] ?? null;
                
                $response .= "📄 **" . $educationTitle . "**\n";
                
                // Get formation info if exists
                if ($formationId) {
                    try {
                        $formationInfo = $this->formationModel->getById($formationId);
                        if ($formationInfo) {
                            $formationTitle = htmlspecialchars($formationInfo['title'] ?? 'Formation');
                            $response .= "📚 **Formation parente** : " . $formationTitle . "\n";
                        }
                    } catch (Exception $e) {
                        error_log("Error fetching formation for education: " . $e->getMessage());
                    }
                }
                
                $desc = !empty($education['description']) ? strip_tags($education['description']) : 'Aucune description disponible';
                $desc = mb_substr($desc, 0, 100, 'UTF-8');
                if (mb_strlen($education['description'] ?? '', 'UTF-8') > 100) $desc .= "...";
                $response .= "📝 " . $desc . "\n";
                
                // Add link to education detail page
                if ($educationId > 0) {
                    $educationUrl = "?controller=education&action=detail&id=" . $educationId;
                    $response .= "🔗 [Voir plus →](" . $educationUrl . ")\n\n";
                } else {
                    $response .= "\n";
                }
                
                $count++;
            }
        }
        
        if (!empty($response)) {
            $response .= "💡 Demandez-moi plus d'informations sur une formation ou éducation spécifique !";
            return $response;
        }

        return "Je n'ai pas trouvé de formation ou éducation correspondant à votre recherche. 😔\n\nEssayez de :\n- 📝 Utiliser des mots-clés du nom ou de la description\n- 🔍 Voir toutes les formations disponibles\n- 💡 Me demander \"Liste toutes les formations\"";
    }
    
    private function getAllFormationsResponse() {
        try {
            $formations = $this->formationModel->getAll()->fetchAll(PDO::FETCH_ASSOC);
            $educations = $this->educationModel->getAll()->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($formations) && empty($educations)) {
                return "Il n'y a aucune formation ou éducation disponible pour le moment. Revenez bientôt ! 📚";
            }
            
            $response = "📚 **Formations et Éducations disponibles** :\n\n";
            
            if (!empty($formations)) {
                $response .= "📖 **Formations** (" . count($formations) . ") :\n";
                foreach ($formations as $formation) {
                    $formationId = $formation['id'] ?? 0;
                    $formationTitle = htmlspecialchars($formation['title'] ?? 'Sans nom');
                    if ($formationId > 0) {
                        $formationUrl = "?controller=formation&action=detail&id=" . $formationId;
                        $response .= "   • **[" . $formationTitle . "](" . $formationUrl . ")**\n";
                    } else {
                        $response .= "   • **" . $formationTitle . "**\n";
                    }
                }
                $response .= "\n";
            }
            
            if (!empty($educations)) {
                $response .= "📝 **Éducations** (" . count($educations) . ") :\n";
                $count = 0;
                foreach ($educations as $education) {
                    if ($count >= 10) break;
                    $educationId = $education['id'] ?? 0;
                    $educationTitle = htmlspecialchars($education['title'] ?? 'Sans nom');
                    if ($educationId > 0) {
                        $educationUrl = "?controller=education&action=detail&id=" . $educationId;
                        $response .= "   • **[" . $educationTitle . "](" . $educationUrl . ")**\n";
                    } else {
                        $response .= "   • **" . $educationTitle . "**\n";
                    }
                    $count++;
                }
                if (count($educations) > 10) {
                    $response .= "   ... et " . (count($educations) - 10) . " autre(s)\n";
                }
            }
            
            $response .= "\n💡 Demandez-moi plus d'informations sur une formation ou éducation spécifique !\n";
            $response .= "🔗 [Voir toutes les formations →](?controller=formation&action=list)";
            return $response;
        } catch (Exception $e) {
            error_log("Error in getAllFormationsResponse: " . $e->getMessage());
            return "Désolé, une erreur s'est produite lors de la récupération des formations. 🔧";
        }
    }
    
    
    // General list question
    private function handleGeneralListQuestion($userMessage) {
        $response = "📋 **Résumé de notre plateforme** :\n\n";
        
        try {
            // Count projects
            $projects = $this->projectModel->getAllProjects();
            $response .= "🌍 **Projets** : " . count($projects) . " projet(s) disponible(s)\n";
            
            // Count events
            $events = $this->eventModel->getAllEvents();
            $response .= "📅 **Événements** : " . count($events) . " événement(s) disponible(s)\n";
            
            // Count games
            $query = "SELECT COUNT(*) as total FROM games WHERE status = 'published'";
            try {
                $checkQuery = $this->conn->query("SHOW COLUMNS FROM games LIKE 'approval_status'");
                if ($checkQuery->rowCount() > 0) {
                    $query .= " AND approval_status = 'approved'";
                }
            } catch (PDOException $e) {}
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $gameCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            $response .= "🎮 **Jeux** : " . $gameCount . " jeu(x) disponible(s)\n\n";
            
            $response .= "💡 Demandez-moi plus de détails sur un sujet spécifique !";
        } catch (Exception $e) {
            error_log("Error in handleGeneralListQuestion: " . $e->getMessage());
            $response = "Je peux vous aider avec les projets, événements, jeux et formations ! Posez-moi une question spécifique !";
        }
        
        return $response;
    }
    
    // Helper methods from ProjectChatbotController
    private function getAllProjectsResponse() {
        try {
            $projects = $this->projectModel->getAllProjects();
            $stats = $this->donationModel->getStatistics();
            
            if (empty($projects)) {
                return "Il n'y a aucun projet disponible pour le moment. Revenez bientôt ! 📅";
            }
            
            $response = "📋 **Liste de tous nos projets** (" . count($projects) . " projet(s)) :\n\n";
            
            foreach ($projects as $project) {
                $donationStats = $this->getProjectDonationStats($project['id']);
                $response .= "🌍 **" . htmlspecialchars($project['title']) . "**\n";
                $response .= "   📁 " . htmlspecialchars($project['category']) . "\n";
                $response .= "   💝 " . $donationStats['count'] . " donation(s) - " . number_format($donationStats['total'], 2, ',', ' ') . "€\n\n";
            }
            
            $response .= "💡 Demandez-moi plus d'informations sur un projet spécifique !";
            return $response;
        } catch (Exception $e) {
            error_log("Error in getAllProjectsResponse: " . $e->getMessage());
            return "Désolé, une erreur s'est produite lors de la récupération des projets. 🔧";
        }
    }
    
    private function handleDonationQuestion($userMessage) {
        try {
            $stats = $this->donationModel->getStatistics();
            $projects = $this->projectModel->getAllProjects();
            
            foreach ($projects as $project) {
                if (stripos($userMessage, $project['title']) !== false) {
                    $donationStats = $this->getProjectDonationStats($project['id']);
                    return "💝 **Donations pour " . htmlspecialchars($project['title']) . "** :\n\n" .
                           "📊 Nombre de donations : **" . $donationStats['count'] . "**\n" .
                           "💰 Montant total : **" . number_format($donationStats['total'], 2, ',', ' ') . "€**\n\n" .
                           "Merci à tous nos généreux donateurs ! 🙏";
                }
            }
            
            $response = "💝 **Statistiques des Donations** :\n\n";
            $response .= "📊 Total de donations : **" . $stats['total_donations'] . "**\n";
            $response .= "💰 Montant total collecté : **" . number_format($stats['total_amount'] ?? 0, 2, ',', ' ') . "€**\n\n";
            
            if (!empty($stats['donations_per_project'])) {
                $response .= "📈 Par projet :\n";
                foreach ($stats['donations_per_project'] as $item) {
                    $projectTitle = $item['title'] ?? 'Projet sans nom';
                    $count = $item['count'] ?? 0;
                    $total = $item['total'] ?? 0;
                    $response .= "   • **" . htmlspecialchars($projectTitle) . "** : " . $count . " don(s) - " . number_format($total, 2, ',', ' ') . "€\n";
                }
            }
            
            return $response;
        } catch (Exception $e) {
            error_log("Error in handleDonationQuestion: " . $e->getMessage());
            return "Désolé, je ne peux pas récupérer les statistiques de donations pour le moment. 🔧";
        }
    }
    
    private function getProjectDonationStats($projectId) {
        try {
            $sql = "SELECT COUNT(*) as count, COALESCE(SUM(amount), 0) as total 
                    FROM donations 
                    WHERE project_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$projectId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return [
                'count' => $result['count'] ?? 0,
                'total' => floatval($result['total'] ?? 0)
            ];
        } catch (Exception $e) {
            error_log("Error getting project donation stats: " . $e->getMessage());
            return ['count' => 0, 'total' => 0];
        }
    }
    
    private function getDetailedProjectInfo($project) {
        $donationStats = $this->getProjectDonationStats($project['id']);
        $createdDate = !empty($project['created_at']) ? date('d/m/Y', strtotime($project['created_at'])) : 'Date inconnue';
        
        $response = "🌍 **" . htmlspecialchars($project['title'] ?? 'Sans nom') . "**\n\n";
        
        if (!empty($project['category'])) {
            $response .= "📁 **Catégorie** : " . htmlspecialchars($project['category']) . "\n";
        }
        
        $response .= "📅 **Date de création** : " . $createdDate . "\n\n";
        
        $response .= "💝 **Donations** :\n";
        $response .= "   • Nombre de donations : **" . $donationStats['count'] . "**\n";
        $response .= "   • Montant total : **" . number_format($donationStats['total'], 2, ',', ' ') . "€**\n\n";
        
        if (!empty($project['description'])) {
            $desc = strip_tags($project['description']);
            $response .= "📝 **Description** :\n" . $desc . "\n\n";
        }
        
        $response .= "💡 Vous pouvez faire un don pour ce projet en visitant notre page de donations !";
        
        return $response;
    }
    
    // Helper methods from EventChatbotController
    private function getAllEventsResponse() {
        try {
            $events = $this->eventModel->getAllEvents();
            
            if (empty($events)) {
                return "Il n'y a aucun événement disponible pour le moment. Revenez bientôt ! 📅";
            }
            
            $response = "📋 **Liste de tous nos événements** (" . count($events) . " événement(s)) :\n\n";
            
            foreach ($events as $event) {
                $dateInfo = $this->getEventDateInfo($event);
                $response .= "📅 **" . htmlspecialchars($event['nom_evenet']) . "**\n";
                $response .= $dateInfo . "\n";
                
                // Add detailed date information
                if (isset($event['date_debut']) && isset($event['date_fin'])) {
                    try {
                        $date_debut = new DateTime($event['date_debut']);
                        $date_fin = new DateTime($event['date_fin']);
                        $response .= "   📆 Début : " . $date_debut->format('d/m/Y à H:i') . "\n";
                        $response .= "   📆 Fin : " . $date_fin->format('d/m/Y à H:i') . "\n";
                    } catch (Exception $e) {
                        error_log("Error parsing dates: " . $e->getMessage());
                    }
                }
                
                $response .= "\n";
            }
            
            $response .= "💡 Demandez-moi plus d'informations sur un événement spécifique !\n";
            $response .= "🔗 [Voir tous les événements →](?action=events)";
            return $response;
        } catch (Exception $e) {
            error_log("Error in getAllEventsResponse: " . $e->getMessage());
            return "Désolé, une erreur s'est produite lors de la récupération des événements. 🔧";
        }
    }
    
    private function handleDateQuestion($userMessage) {
        try {
            $events = $this->eventModel->getAllEvents();
            
            if (empty($events)) {
                return "Il n'y a aucun événement disponible pour le moment. 📅";
            }
            
            // Check if asking about specific event
            foreach ($events as $event) {
                if (stripos($userMessage, $event['nom_evenet']) !== false) {
                    return $this->getDetailedEventInfo($event);
                }
            }
            
            // General date information
            $response = "📅 **Dates des Événements** :\n\n";
            
            foreach ($events as $event) {
                $dateInfo = $this->getEventDateInfo($event);
                $response .= "📅 **" . htmlspecialchars($event['nom_evenet']) . "**\n";
                $response .= $dateInfo . "\n\n";
            }
            
            return $response;
        } catch (Exception $e) {
            error_log("Error in handleDateQuestion: " . $e->getMessage());
            return "Désolé, je ne peux pas récupérer les dates des événements pour le moment. 🔧";
        }
    }
    
    private function getDetailedEventInfo($event) {
        $dateInfo = $this->getEventDateInfo($event);
        
        $response = "📅 **" . htmlspecialchars($event['nom_evenet'] ?? 'Sans nom') . "**\n\n";
        
        $response .= "🕐 **Dates et Horaires** :\n";
        $response .= $dateInfo . "\n\n";
        
        // Extract detailed date information
        if (isset($event['date_debut']) && isset($event['date_fin'])) {
            try {
                $date_debut = new DateTime($event['date_debut']);
                $date_fin = new DateTime($event['date_fin']);
                
                $response .= "📆 **Date de début** : " . $date_debut->format('d/m/Y à H:i') . "\n";
                $response .= "📆 **Date de fin** : " . $date_fin->format('d/m/Y à H:i') . "\n\n";
                
                $interval = $date_debut->diff($date_fin);
                if ($interval->days > 0) {
                    $response .= "⏱️ **Durée** : " . $interval->days . " jour(s)";
                    if ($interval->h > 0) {
                        $response .= " et " . $interval->h . " heure(s)";
                    }
                    $response .= "\n\n";
                } else {
                    $hours = $interval->h;
                    $minutes = $interval->i;
                    if ($hours > 0) {
                        $response .= "⏱️ **Durée** : " . $hours . " heure(s)";
                        if ($minutes > 0) {
                            $response .= " et " . $minutes . " minute(s)";
                        }
                    } else {
                        $response .= "⏱️ **Durée** : " . $minutes . " minute(s)";
                    }
                    $response .= "\n\n";
                }
            } catch (Exception $e) {
                error_log("Error parsing dates in getDetailedEventInfo: " . $e->getMessage());
            }
        }
        
        if (!empty($event['description'])) {
            $desc = strip_tags($event['description']);
            $response .= "📝 **Description** :\n" . $desc . "\n\n";
        }
        
        $response .= "💡 Vous pouvez participer à cet événement en cliquant sur le bouton \"Participer\" !\n";
        $response .= "🔗 [Voir tous les événements →](?action=events)";
        
        return $response;
    }
    
    private function handleSpecificEventQuestion($userMessage, $originalMessage) {
        $eventName = $this->extractEventName($userMessage, $originalMessage);
        
        if (empty($eventName)) {
            return "Quel événement vous intéresse ? Dites-moi le nom de l'événement et je vous donnerai toutes les informations ! 📅";
        }
        
        try {
            $events = $this->eventModel->getAllEvents();
            
            $bestMatch = null;
            $bestScore = 0;
            
            foreach ($events as $event) {
                $name = strtolower($event['nom_evenet'] ?? '');
                $score = 0;
                
                if ($name === strtolower($eventName)) {
                    $score = 100;
                } elseif (strpos($name, strtolower($eventName)) !== false) {
                    $score = 50;
                } elseif (strpos(strtolower($eventName), $name) !== false) {
                    $score = 30;
                }
                
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestMatch = $event;
                }
            }
            
            if ($bestMatch && $bestScore > 0) {
                return $this->getDetailedEventInfo($bestMatch);
            } else {
                return "Je n'ai pas trouvé d'événement nommé **'" . htmlspecialchars($eventName) . "'**. 😔\n\n💡 Essayez de :\n- Vérifier l'orthographe\n- Utiliser un nom partiel\n- [Voir tous nos événements](?action=events)";
            }
        } catch (Exception $e) {
            error_log("Error in handleSpecificEventQuestion: " . $e->getMessage());
            return "Désolé, je rencontre un problème. Veuillez réessayer. 🔧";
        }
    }
    
    private function extractEventName($message, $originalMessage) {
        $cleaned = preg_replace('/\b(parle|dis|raconte|explique|décris|informe|donne|montre|qu\'est|qu\'est-ce|what is|what\'s|c\'est quoi|à propos|au sujet|sur|de|informations|info|détails|détail|événement|event|évènement|événements|events|évènements)\b/i', '', $message);
        $cleaned = trim($cleaned);
        
        if (preg_match('/["\']([^"\']+)["\']/', $originalMessage, $matches)) {
            return $matches[1];
        }
        
        if (preg_match('/\b(parle|dis|raconte|explique|décris|informe|donne|montre)\s+(?:sur|de|à propos|au sujet)?\s+([a-zA-Z0-9\s]+)/i', $originalMessage, $matches)) {
            return trim($matches[2]);
        }
        
        if (preg_match('/\b(qu\'est|qu\'est-ce|what is|what\'s|c\'est quoi)\s+([a-zA-Z0-9\s]+)/i', $originalMessage, $matches)) {
            return trim($matches[2]);
        }
        
        return $cleaned;
    }
    
    private function getEventDateInfo($event) {
        if (isset($event['date_debut']) && isset($event['date_fin'])) {
            try {
                $date_debut = new DateTime($event['date_debut']);
                $date_fin = new DateTime($event['date_fin']);
                
                $now = new DateTime();
                $isPast = $date_fin < $now;
                $isOngoing = $date_debut <= $now && $date_fin >= $now;
                
                $status = $isPast ? '🔴 Passé' : ($isOngoing ? '🟢 En cours' : '🟡 À venir');
                
                $response = "   " . $status . "\n";
                $response .= "   🕐 Début : " . $date_debut->format('d/m/Y à H:i') . "\n";
                $response .= "   🕐 Fin : " . $date_fin->format('d/m/Y à H:i') . "\n";
                
                $interval = $date_debut->diff($date_fin);
                if ($interval->days > 0) {
                    $response .= "   ⏱️ Durée : " . $interval->days . " jour(s)";
                    if ($interval->h > 0) {
                        $response .= " et " . $interval->h . " heure(s)";
                    }
                } else {
                    $hours = $interval->h;
                    $minutes = $interval->i;
                    if ($hours > 0) {
                        $response .= "   ⏱️ Durée : " . $hours . " heure(s)";
                        if ($minutes > 0) {
                            $response .= " et " . $minutes . " minute(s)";
                        }
                    } else {
                        $response .= "   ⏱️ Durée : " . $minutes . " minute(s)";
                    }
                }
                
                return $response;
            } catch (Exception $e) {
                error_log("Error parsing dates: " . $e->getMessage());
            }
        }
        
        return "   📅 Informations de date non disponibles";
    }
    
    // Helper methods from GameChatbotController
    private function getAllGamesResponse() {
        try {
            $query = "SELECT g.*, gc.name as category_name,
                      (SELECT AVG(rating) FROM game_ratings WHERE game_id = g.id) as rating_average,
                      (SELECT COUNT(*) FROM game_ratings WHERE game_id = g.id) as rating_count
                      FROM games g 
                      LEFT JOIN game_categories gc ON g.category_id = gc.id 
                      WHERE g.status = 'published'";
            
            try {
                $checkQuery = $this->conn->query("SHOW COLUMNS FROM games LIKE 'approval_status'");
                if ($checkQuery->rowCount() > 0) {
                    $query .= " AND g.approval_status = 'approved'";
                }
            } catch (PDOException $e) {}
            
            $query .= " ORDER BY g.created_at DESC LIMIT 10";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $countQuery = "SELECT COUNT(*) as total FROM games WHERE status = 'published'";
            try {
                $checkQuery = $this->conn->query("SHOW COLUMNS FROM games LIKE 'approval_status'");
                if ($checkQuery->rowCount() > 0) {
                    $countQuery .= " AND approval_status = 'approved'";
                }
            } catch (PDOException $e) {}
            $countStmt = $this->conn->prepare($countQuery);
            $countStmt->execute();
            $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? count($games);
            
            if (empty($games)) {
                return "Il n'y a pas encore de jeux dans notre collection. 😢\n\n💡 **Vous pouvez :**\n- [Ajouter un jeu](?action=add_game) pour enrichir notre collection\n- Revenir plus tard quand d'autres jeux seront disponibles";
            }
            
            $response = "🎮 Nous avons **" . $total . " jeu(x)** dans notre collection !\n\n";
            $response .= "Voici les **" . min(10, count($games)) . " derniers jeux** :\n\n";
            
            foreach ($games as $game) {
                $response .= "🎯 **" . htmlspecialchars($game['name'] ?? 'Sans nom') . "**";
                if (!empty($game['category_name'])) {
                    $response .= " - 📁 " . htmlspecialchars($game['category_name']);
                }
                if (!empty($game['rating_average']) && $game['rating_average'] > 0) {
                    $response .= " - ⭐ " . round($game['rating_average'], 1) . "/5";
                }
                $response .= "\n";
            }
            
            $response .= "\n💡 [Voir tous les jeux](?action=games) | [Recherche avancée](?action=search_games)";
            
            return $response;
        } catch (PDOException $e) {
            error_log("Error in getAllGamesResponse: " . $e->getMessage());
            return "Désolé, je rencontre un problème pour récupérer la liste des jeux. Veuillez réessayer. 🔧";
        }
    }
    
    private function getDetailedGameInfo($game) {
        $response = "🎮 **" . htmlspecialchars($game['name'] ?? 'Sans nom') . "**\n\n";
        
        if (!empty($game['category_name'])) {
            $response .= "📁 **Catégorie :** " . htmlspecialchars($game['category_name']) . "\n";
        }
        
        if (!empty($game['rating_average']) && $game['rating_average'] > 0) {
            $rating = round($game['rating_average'], 1);
            $countRatings = $game['rating_count'] ?? 0;
            $stars = str_repeat('⭐', min(5, round($rating)));
            $response .= "⭐ **Note :** " . $stars . " " . $rating . "/5 (" . $countRatings . " avis)\n";
        } else {
            $response .= "⭐ **Note :** Pas encore noté\n";
        }
        
        if (!empty($game['description'])) {
            $desc = strip_tags($game['description']);
            $response .= "\n📝 **Description :**\n" . $desc . "\n";
        }
        
        if (!empty($game['impact_social'])) {
            $response .= "\n🌟 **Impact Social :**\n" . htmlspecialchars($game['impact_social']) . "\n";
        }
        
        $link = "?action=game_details&id=" . ($game['id'] ?? '');
        $response .= "\n🔗 [Voir tous les détails et la vidéo](" . $link . ")";
        
        return $response;
    }
}
?>

