<?php
require_once "models/Game.php";
require_once "config/database.php";

class GameChatbotController {
    
    private $gameModel;
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->gameModel = new Game($this->conn);
    }

    public function handleRequest() {
        // Ensure we're sending JSON
        header('Content-Type: application/json');
        
        // Get JSON input
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
            echo json_encode([
                'success' => false,
                'error' => 'An error occurred processing your request.'
            ]);
        }
    }

    /**
     * Get a response based on real database content for games
     * 
     * @param string $userMessage The user's message
     * @return string The AI's response
     */
    public function getResponse($userMessage) {
        $message = strtolower(trim($userMessage));
        $originalMessage = $userMessage;
        
        // Log for debugging
        error_log("GameChatbot - User message: " . $userMessage);
        
        // 1. Basic Greetings
        if (preg_match('/\b(hello|hi|bonjour|salut|coucou|hey|yo)\b/i', $userMessage)) {
            return "Bonjour ! 👋 Je suis **G-Bot**, votre assistant intelligent pour les jeux ! 🎮\n\nJe peux vous aider à :\n- 🔍 Trouver des jeux par nom, catégorie ou description\n- 📚 Répondre à vos questions sur nos jeux\n- 🎯 Vous guider dans notre collection\n- ⭐ Vous donner des informations détaillées sur chaque jeu\n\nQue souhaitez-vous savoir ?";
        }
        
        // 1.5. Questions about "what games do you have" or "list all games"
        if (preg_match('/\b(quels|quelles|liste|tous|tout|combien|nombre|total)\b.*\b(jeux|games|jeu|game)\b/i', $userMessage) || 
            preg_match('/\b(jeux|games|jeu|game)\b.*\b(quels|quelles|liste|tous|tout|combien|nombre|total|avez|disponible|avoir)\b/i', $userMessage) ||
            preg_match('/\b(qu\'est|qu\'y|y a|il y a)\b.*\b(jeux|games|jeu|game)\b/i', $userMessage)) {
            return $this->getAllGamesResponse();
        }

        // 2. Extract Keywords (Simple NLP) - Improved
        $stopWords = ['je', 'tu', 'il', 'elle', 'nous', 'vous', 'ils', 'elles', 'le', 'la', 'les', 'un', 'une', 'des', 'de', 'du', 'au', 'aux', 'et', 'ou', 'mais', 'donc', 'or', 'ni', 'car', 'ce', 'cet', 'cette', 'ces', 'mon', 'ton', 'son', 'ma', 'ta', 'sa', 'mes', 'tes', 'ses', 'notre', 'votre', 'leur', 'nos', 'vos', 'leurs', 'qui', 'que', 'quoi', 'dont', 'où', 'quand', 'comment', 'pourquoi', 'quel', 'quelle', 'quels', 'quelles', 'est', 'sont', 'a', 'ont', 'veux', 'voudrais', 'souhaite', 'aimerais', 'apprendre', 'chercher', 'trouver', 'avoir', 'besoin', 'aide', 'sur', 'dans', 'par', 'pour', 'vers', 'avec', 'sans', 'sous', 'jeu', 'jeux', 'game', 'games', 'les', 'des', 'tous', 'tout', 'toute', 'toutes'];
        
        // Remove punctuation and special characters
        $cleanMessage = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $userMessage);
        $words = explode(' ', strtolower($cleanMessage));
        
        $keywords = [];
        foreach ($words as $word) {
            $word = trim($word);
            if (!empty($word) && !in_array($word, $stopWords) && strlen($word) > 2) {
                $keywords[] = $word;
            }
        }

        // If no keywords found, check if it's a question pattern
        if (empty($keywords)) {
            // Check for question patterns
            if (preg_match('/\b(quel|quelle|quels|quelles|combien|où|comment|pourquoi)\b/i', $userMessage)) {
                // It's a question, try to extract meaningful words
                $keywords = array_filter(explode(' ', $cleanMessage), function($w) use ($stopWords) {
                    return !in_array($w, $stopWords) && strlen($w) > 2;
                });
            }
            
            // If still empty, use the original message (cleaned)
            if (empty($keywords)) {
                $keywords = [preg_replace('/[^\p{L}\p{N}]/u', '', $userMessage)];
            }
        }
        
        error_log("GameChatbot - Extracted keywords: " . implode(', ', $keywords));

        // 2.5. Handle specific game questions (e.g., "Tell me about [game name]", "What is [game name]?")
        if (preg_match('/\b(parle|dis|raconte|explique|décris|informe|donne|montre)\b.*\b(sur|de|à propos|au sujet)\b/i', $userMessage) ||
            preg_match('/\b(qu\'est|qu\'est-ce|what is|what\'s|c\'est quoi|qu\'est-ce que c\'est)\b/i', $userMessage) ||
            preg_match('/\b(à propos|au sujet|sur|de|informations|info|détails|détail)\b.*\b(jeu|game)\b/i', $userMessage)) {
            return $this->handleSpecificGameQuestion($userMessage, $originalMessage);
        }
        
        // 2.6. Handle rating questions
        if (preg_match('/\b(note|rating|étoile|star|moyenne|average|avis|review)\b/i', $userMessage)) {
            return $this->handleRatingQuestion($userMessage);
        }
        
        // 2.7. Handle category questions
        if (preg_match('/\b(catégorie|category|type|genre)\b/i', $userMessage)) {
            return $this->handleCategoryQuestion($userMessage);
        }
        
        // 3. Fetch Real Data from Database with ratings
        try {
            // Try with approval_status first, fallback if column doesn't exist
            $query = "SELECT g.*, gc.name as category_name,
                      (SELECT AVG(rating) FROM game_ratings WHERE game_id = g.id) as rating_average,
                      (SELECT COUNT(*) FROM game_ratings WHERE game_id = g.id) as rating_count
                      FROM games g 
                      LEFT JOIN game_categories gc ON g.category_id = gc.id 
                      WHERE g.status = 'published'";
            
            // Check if approval_status column exists
            try {
                $checkQuery = $this->conn->query("SHOW COLUMNS FROM games LIKE 'approval_status'");
                if ($checkQuery->rowCount() > 0) {
                    $query .= " AND g.approval_status = 'approved'";
                }
            } catch (PDOException $e) {
                // Column doesn't exist, continue without it
            }
            
            $query .= " ORDER BY g.created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("GameChatbot - Found " . count($games) . " games in database");
        } catch (PDOException $e) {
            error_log("Error fetching games: " . $e->getMessage());
            $games = [];
        }

        // 4. Search for matches in Games - Enhanced matching
        $foundItems = [];
        
        // Check if user is asking for a specific game by name
        $isNameSearch = preg_match('/\b(jeu|game)\b.*\b(nommé|appelé|intitulé|nom|name)\b/i', $userMessage) ||
                       preg_match('/\b(nommé|appelé|intitulé|nom|name)\b.*\b(jeu|game)\b/i', $userMessage);
        
        foreach ($games as $game) {
            $score = 0;
            $name = strtolower($game['name'] ?? '');
            $description = strtolower($game['description'] ?? '');
            $impactSocial = strtolower($game['impact_social'] ?? '');
            $categoryName = strtolower($game['category_name'] ?? '');
            
            // Build search string from all keywords
            $searchString = implode(' ', $keywords);
            
            // Exact name match (highest priority)
            if ($name === $searchString) {
                $score += 100;
            } elseif (strpos($name, $searchString) !== false || strpos($searchString, $name) !== false) {
                $score += 50; // Partial name match
            }
            
            // Individual keyword matching
            foreach ($keywords as $keyword) {
                // Name matching (highest weight)
                if (strpos($name, $keyword) !== false) {
                    $score += 15; // Increased weight for name matches
                }
                // Description matching
                if (strpos($description, $keyword) !== false) {
                    $score += 4;
                }
                // Impact social matching
                if (strpos($impactSocial, $keyword) !== false) {
                    $score += 3;
                }
                // Category matching
                if (strpos($categoryName, $keyword) !== false) {
                    $score += 8;
                }
            }
            
            // If it's a name search, require higher score
            $threshold = $isNameSearch ? 15 : 5;
            
            if ($score >= $threshold) {
                $game['score'] = $score;
                $foundItems[] = $game;
            }
        }

        // Sort by score
        usort($foundItems, function($a, $b) {
            return $b['score'] - $a['score'];
        });

        // 5. Construct Response based on findings
        if (!empty($foundItems)) {
            $totalCount = count($foundItems);
            
            // If only one game found and it's a high score match, give detailed info
            if ($totalCount === 1 && $foundItems[0]['score'] >= 10) {
                return $this->getDetailedGameInfo($foundItems[0]);
            }
            
            $response = "🎯 J'ai trouvé **" . $totalCount . " jeu(x)** correspondant à votre recherche :\n\n";
            
            // Limit to 5 results for better UX
            $count = 0;
            foreach ($foundItems as $game) {
                if ($count >= 5) break;
                
                $response .= "🎮 **" . htmlspecialchars($game['name'] ?? 'Sans nom') . "**\n";
                
                if (!empty($game['category_name'])) {
                    $response .= "📁 Catégorie : " . htmlspecialchars($game['category_name']) . "\n";
                }
                
                // Add rating if available
                if (!empty($game['rating_average']) && $game['rating_average'] > 0) {
                    $rating = round($game['rating_average'], 1);
                    $countRatings = $game['rating_count'] ?? 0;
                    $response .= "⭐ Note : " . $rating . "/5 (" . $countRatings . " avis)\n";
                }
                
                // Add a short snippet of description
                $desc = !empty($game['description']) ? strip_tags($game['description']) : 'Aucune description disponible';
                $desc = mb_substr($desc, 0, 120, 'UTF-8');
                if (mb_strlen($game['description'] ?? '', 'UTF-8') > 120) $desc .= "...";
                $response .= "📝 " . $desc . "\n";
                
                // Add impact social if available
                if (!empty($game['impact_social'])) {
                    $impact = mb_substr(strip_tags($game['impact_social']), 0, 80, 'UTF-8');
                    $response .= "🌟 Impact : " . $impact . "\n";
                }
                
                // Add a link
                $link = "?action=game_details&id=" . ($game['id'] ?? '');
                $response .= "🔗 [Voir les détails](" . $link . ")\n\n";
                $count++;
            }
            
            if ($totalCount > 5) {
                $response .= "💡 *Et " . ($totalCount - 5) . " autre(s) jeu(x) ! Utilisez la recherche avancée pour voir tous les résultats.*";
            }
            
            return $response;
        }
        
        // 6. Fallback responses for common questions
        if (preg_match('/\b(insc|créer|compte|register|signup)\b/', $message)) {
            return "Pour vous inscrire, cliquez sur le bouton 'Inscription' en haut à droite. C'est gratuit et ça prend 2 minutes ! 📝";
        }
        
        if (preg_match('/\b(connect|login|connexion|se connecter)\b/', $message)) {
            return "Vous pouvez vous connecter via le bouton 'Connexion' en haut à droite. Si vous avez oublié votre mot de passe, utilisez le lien 'Mot de passe oublié'. 🔐";
        }
        
        if (preg_match('/\b(ajouter|créer|soumettre|proposer|nouveau)\b.*\b(jeu|game)\b/', $message)) {
            return "Pour ajouter un jeu, allez dans la section 'Jeux' et cliquez sur 'Ajouter un jeu'. Vous pourrez soumettre votre jeu qui sera ensuite examiné par notre équipe avant publication. 🎮✨";
        }
        
        if (preg_match('/\b(catégorie|categories|type)\b/', $message)) {
            return "Nos jeux sont organisés par catégories. Vous pouvez voir toutes les catégories disponibles dans la section 'Recherche Jeux'. Chaque jeu appartient à une catégorie spécifique. 📁";
        }
        
        if (preg_match('/\b(impact|social|bénéfice|utile)\b/', $message)) {
            return "Chaque jeu sur notre plateforme a un impact social positif ! L'impact social décrit comment le jeu contribue à la société, à l'éducation, ou à la communauté. Vous pouvez voir l'impact de chaque jeu sur sa page de détails. 🌟";
        }
        
        // 7. Suggest searching all games if no match
        if (count($games) > 0) {
            return "Je n'ai pas trouvé de jeu correspondant à **'" . htmlspecialchars(implode(" ", $keywords)) . "'**. 😔\n\n💡 **Suggestions :**\n- Essayez des termes plus précis\n- Consultez tous nos jeux dans la section [Jeux](?action=games)\n- Utilisez la [recherche avancée](?action=search_games)\n- Posez-moi une question sur les catégories ou comment ajouter un jeu ! 🎮";
        } else {
            return "Il n'y a pas encore de jeux dans notre collection. 😢\n\n💡 **Vous pouvez :**\n- [Ajouter un jeu](?action=add_game) pour enrichir notre collection\n- Revenir plus tard quand d'autres jeux seront disponibles\n\nMerci de votre compréhension ! 🎮";
        }
    }
    
    /**
     * Get all games response
     */
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
            
            // Get total count
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
    
    /**
     * Handle specific game questions
     */
    private function handleSpecificGameQuestion($userMessage, $originalMessage) {
        // Extract game name from question
        $gameName = $this->extractGameName($userMessage, $originalMessage);
        
        if (empty($gameName)) {
            return "Quel jeu vous intéresse ? Dites-moi le nom du jeu et je vous donnerai toutes les informations ! 🎮";
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
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Find best match
            $bestMatch = null;
            $bestScore = 0;
            
            foreach ($games as $game) {
                $name = strtolower($game['name'] ?? '');
                $score = 0;
                
                // Exact match
                if ($name === strtolower($gameName)) {
                    $score = 100;
                } elseif (strpos($name, strtolower($gameName)) !== false) {
                    $score = 50;
                } elseif (strpos(strtolower($gameName), $name) !== false) {
                    $score = 30;
                }
                
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestMatch = $game;
                }
            }
            
            if ($bestMatch && $bestScore > 0) {
                return $this->getDetailedGameInfo($bestMatch);
            } else {
                return "Je n'ai pas trouvé de jeu nommé **'" . htmlspecialchars($gameName) . "'**. 😔\n\n💡 Essayez de :\n- Vérifier l'orthographe\n- Utiliser un nom partiel\n- [Voir tous nos jeux](?action=games)";
            }
        } catch (PDOException $e) {
            error_log("Error in handleSpecificGameQuestion: " . $e->getMessage());
            return "Désolé, je rencontre un problème. Veuillez réessayer. 🔧";
        }
    }
    
    /**
     * Extract game name from user message
     */
    private function extractGameName($userMessage, $originalMessage) {
        // Remove common question words
        $cleaned = preg_replace('/\b(parle|dis|raconte|explique|décris|informe|donne|montre|qu\'est|qu\'est-ce|what is|what\'s|c\'est quoi|à propos|au sujet|sur|de|informations|info|détails|détail|jeu|game|jeux|games)\b/i', '', $userMessage);
        $cleaned = trim($cleaned);
        
        // Try to extract quoted text
        if (preg_match('/["\']([^"\']+)["\']/', $originalMessage, $matches)) {
            return $matches[1];
        }
        
        // Try to extract text after common patterns
        if (preg_match('/\b(parle|dis|raconte|explique|décris|informe|donne|montre)\s+(?:sur|de|à propos|au sujet)?\s+([a-zA-Z0-9\s]+)/i', $originalMessage, $matches)) {
            return trim($matches[2]);
        }
        
        if (preg_match('/\b(qu\'est|qu\'est-ce|what is|what\'s|c\'est quoi)\s+([a-zA-Z0-9\s]+)/i', $originalMessage, $matches)) {
            return trim($matches[2]);
        }
        
        // Return cleaned message if it's short enough (likely a game name)
        if (strlen($cleaned) > 2 && strlen($cleaned) < 50) {
            return $cleaned;
        }
        
        return '';
    }
    
    /**
     * Get detailed information about a specific game
     */
    private function getDetailedGameInfo($game) {
        $response = "🎮 **" . htmlspecialchars($game['name'] ?? 'Sans nom') . "**\n\n";
        
        if (!empty($game['category_name'])) {
            $response .= "📁 **Catégorie :** " . htmlspecialchars($game['category_name']) . "\n";
        }
        
        // Rating
        if (!empty($game['rating_average']) && $game['rating_average'] > 0) {
            $rating = round($game['rating_average'], 1);
            $countRatings = $game['rating_count'] ?? 0;
            $stars = str_repeat('⭐', min(5, round($rating)));
            $response .= "⭐ **Note :** " . $stars . " " . $rating . "/5 (" . $countRatings . " avis)\n";
        } else {
            $response .= "⭐ **Note :** Pas encore noté\n";
        }
        
        // Description
        if (!empty($game['description'])) {
            $desc = strip_tags($game['description']);
            $response .= "\n📝 **Description :**\n" . $desc . "\n";
        }
        
        // Impact social
        if (!empty($game['impact_social'])) {
            $response .= "\n🌟 **Impact Social :**\n" . htmlspecialchars($game['impact_social']) . "\n";
        }
        
        // Video
        if (!empty($game['demo_url'])) {
            $response .= "\n🎥 **Vidéo de démonstration disponible !**\n";
        }
        
        // Link
        $link = "?action=game_details&id=" . ($game['id'] ?? '');
        $response .= "\n🔗 [Voir tous les détails et la vidéo](" . $link . ")";
        
        return $response;
    }
    
    /**
     * Handle rating questions
     */
    private function handleRatingQuestion($userMessage) {
        try {
            $query = "SELECT g.id, g.name, 
                      (SELECT AVG(rating) FROM game_ratings WHERE game_id = g.id) as rating_average,
                      (SELECT COUNT(*) FROM game_ratings WHERE game_id = g.id) as rating_count
                      FROM games g 
                      WHERE g.status = 'published'";
            
            try {
                $checkQuery = $this->conn->query("SHOW COLUMNS FROM games LIKE 'approval_status'");
                if ($checkQuery->rowCount() > 0) {
                    $query .= " AND g.approval_status = 'approved'";
                }
            } catch (PDOException $e) {}
            
            $query .= " HAVING rating_average > 0 ORDER BY rating_average DESC, rating_count DESC LIMIT 5";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $topGames = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($topGames)) {
                return "Aucun jeu n'a encore été noté. Soyez le premier à noter un jeu ! ⭐";
            }
            
            $response = "⭐ **Top jeux les mieux notés :**\n\n";
            $rank = 1;
            foreach ($topGames as $game) {
                $rating = round($game['rating_average'], 1);
                $count = $game['rating_count'] ?? 0;
                $stars = str_repeat('⭐', min(5, round($rating)));
                $response .= $rank . ". **" . htmlspecialchars($game['name']) . "** - " . $stars . " " . $rating . "/5 (" . $count . " avis)\n";
                $rank++;
            }
            
            $response .= "\n💡 [Voir tous les jeux](?action=games)";
            
            return $response;
        } catch (PDOException $e) {
            error_log("Error in handleRatingQuestion: " . $e->getMessage());
            return "Désolé, je rencontre un problème pour récupérer les notes. 🔧";
        }
    }
    
    /**
     * Handle category questions
     */
    private function handleCategoryQuestion($userMessage) {
        try {
            $query = "SELECT gc.*, COUNT(g.id) as game_count 
                      FROM game_categories gc 
                      LEFT JOIN games g ON gc.id = g.category_id AND g.status = 'published'";
            
            try {
                $checkQuery = $this->conn->query("SHOW COLUMNS FROM games LIKE 'approval_status'");
                if ($checkQuery->rowCount() > 0) {
                    $query .= " AND (g.approval_status = 'approved' OR g.id IS NULL)";
                }
            } catch (PDOException $e) {}
            
            $query .= " GROUP BY gc.id ORDER BY gc.name";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($categories)) {
                return "Il n'y a pas encore de catégories définies. 📁";
            }
            
            $response = "📁 **Nos catégories de jeux :**\n\n";
            foreach ($categories as $cat) {
                $response .= "• **" . htmlspecialchars($cat['name']) . "**";
                if (!empty($cat['description'])) {
                    $response .= " - " . htmlspecialchars($cat['description']);
                }
                $response .= " (" . ($cat['game_count'] ?? 0) . " jeu(x))\n";
            }
            
            $response .= "\n💡 [Rechercher par catégorie](?action=search_games)";
            
            return $response;
        } catch (PDOException $e) {
            error_log("Error in handleCategoryQuestion: " . $e->getMessage());
            return "Désolé, je rencontre un problème pour récupérer les catégories. 🔧";
        }
    }
}
?>

