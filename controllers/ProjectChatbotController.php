<?php
require_once "models/Project.php";
require_once "models/Donation.php";
require_once "config/database.php";

class ProjectChatbotController {
    
    private $projectModel;
    private $donationModel;
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->projectModel = new Project($this->conn);
        $this->donationModel = new Donation($this->conn);
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
            error_log("ProjectChatbot error: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'An error occurred processing your request.'
            ]);
        }
    }

    /**
     * Get a response based on real database content for projects
     */
    public function getResponse($userMessage) {
        $message = strtolower(trim($userMessage));
        $originalMessage = $userMessage;
        
        error_log("ProjectChatbot - User message: " . $userMessage);
        
        // 1. Basic Greetings
        if (preg_match('/\b(hello|hi|bonjour|salut|coucou|hey|yo)\b/i', $userMessage)) {
            return "Bonjour ! 👋 Je suis **P-Bot**, votre assistant intelligent pour les projets ! 🌍\n\nJe peux vous aider à :\n- 🔍 Trouver des projets par nom, catégorie ou description\n- 💝 Vous informer sur les donations et statistiques\n- 📚 Répondre à vos questions sur nos projets\n- 🎯 Vous guider dans notre collection\n\nQue souhaitez-vous savoir ?";
        }
        
        // 2. Questions about "what projects do you have" or "list all projects"
        if (preg_match('/\b(quels|quelles|liste|tous|tout|combien|nombre|total)\b.*\b(projets|projects|projet|project)\b/i', $userMessage) || 
            preg_match('/\b(projets|projects|projet|project)\b.*\b(quels|quelles|liste|tous|tout|combien|nombre|total|avez|disponible|avoir)\b/i', $userMessage) ||
            preg_match('/\b(qu\'est|qu\'y|y a|il y a)\b.*\b(projets|projects|projet|project)\b/i', $userMessage)) {
            return $this->getAllProjectsResponse();
        }

        // 3. Questions about donations
        if (preg_match('/\b(donation|donations|don|dons)\b/i', $userMessage)) {
            return $this->handleDonationQuestion($userMessage);
        }

        // 4. Extract Keywords
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
            if (preg_match('/\b(quel|quelle|quels|quelles|combien|où|comment|pourquoi)\b/i', $userMessage)) {
                $keywords = array_filter(explode(' ', $cleanMessage), function($w) use ($stopWords) {
                    return !in_array($w, $stopWords) && strlen($w) > 2;
                });
            }
            if (empty($keywords)) {
                $keywords = [preg_replace('/[^\p{L}\p{N}]/u', '', $userMessage)];
            }
        }
        
        error_log("ProjectChatbot - Extracted keywords: " . implode(', ', $keywords));

        // 5. Handle specific project questions
        if (preg_match('/\b(parle|dis|raconte|explique|décris|informe|donne|montre)\b.*\b(sur|de|à propos|au sujet)\b/i', $userMessage) ||
            preg_match('/\b(qu\'est|qu\'est-ce|what is|what\'s|c\'est quoi|qu\'est-ce que c\'est)\b/i', $userMessage) ||
            preg_match('/\b(à propos|au sujet|sur|de|informations|info|détails|détail)\b.*\b(projet|project)\b/i', $userMessage)) {
            return $this->handleSpecificProjectQuestion($userMessage, $originalMessage);
        }
        
        // 6. Handle category questions
        if (preg_match('/\b(catégorie|category|type|genre)\b/i', $userMessage)) {
            return $this->handleCategoryQuestion($userMessage);
        }

        // 7. Fetch Real Data from Database
        try {
            $projects = $this->projectModel->getAllProjects();
            error_log("ProjectChatbot - Found " . count($projects) . " projects in database");
        } catch (Exception $e) {
            error_log("Error fetching projects: " . $e->getMessage());
            $projects = [];
        }

        // 8. Search for matches in Projects
        $foundItems = [];
        $searchString = implode(' ', $keywords);
        
        foreach ($projects as $project) {
            $score = 0;
            $title = strtolower($project['title'] ?? '');
            $description = strtolower($project['description'] ?? '');
            $category = strtolower($project['category'] ?? '');
            
            // Exact title match
            if ($title === $searchString) {
                $score += 100;
            } elseif (strpos($title, $searchString) !== false || strpos($searchString, $title) !== false) {
                $score += 50;
            }
            
            // Individual keyword matching
            foreach ($keywords as $keyword) {
                if (strpos($title, $keyword) !== false) {
                    $score += 15;
                }
                if (strpos($description, $keyword) !== false) {
                    $score += 5;
                }
                if (strpos($category, $keyword) !== false) {
                    $score += 10;
                }
            }
            
            if ($score >= 5) {
                $project['score'] = $score;
                $foundItems[] = $project;
            }
        }

        // Sort by score
        usort($foundItems, function($a, $b) {
            return $b['score'] - $a['score'];
        });

        // 9. Construct Response
        if (!empty($foundItems)) {
            $totalCount = count($foundItems);
            
            if ($totalCount === 1 && $foundItems[0]['score'] >= 10) {
                return $this->getDetailedProjectInfo($foundItems[0]);
            }
            
            $response = "🎯 J'ai trouvé **" . $totalCount . " projet(s)** correspondant à votre recherche :\n\n";
            
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
            
            // Check if asking about specific project donations
            foreach ($projects as $project) {
                if (stripos($userMessage, $project['title']) !== false) {
                    $donationStats = $this->getProjectDonationStats($project['id']);
                    return "💝 **Donations pour " . htmlspecialchars($project['title']) . "** :\n\n" .
                           "📊 Nombre de donations : **" . $donationStats['count'] . "**\n" .
                           "💰 Montant total : **" . number_format($donationStats['total'], 2, ',', ' ') . "€**\n\n" .
                           "Merci à tous nos généreux donateurs ! 🙏";
                }
            }
            
            // General donation statistics
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

    private function handleSpecificProjectQuestion($userMessage, $originalMessage) {
        $projectName = $this->extractProjectName($userMessage, $originalMessage);
        
        if (empty($projectName)) {
            return "Quel projet vous intéresse ? Dites-moi le nom du projet et je vous donnerai toutes les informations ! 🌍";
        }
        
        try {
            $projects = $this->projectModel->getAllProjects();
            
            $bestMatch = null;
            $bestScore = 0;
            
            foreach ($projects as $project) {
                $title = strtolower($project['title'] ?? '');
                $score = 0;
                
                if ($title === strtolower($projectName)) {
                    $score = 100;
                } elseif (strpos($title, strtolower($projectName)) !== false) {
                    $score = 50;
                }
                
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestMatch = $project;
                }
            }
            
            if ($bestMatch && $bestScore >= 30) {
                return $this->getDetailedProjectInfo($bestMatch);
            }
        } catch (Exception $e) {
            error_log("Error in handleSpecificProjectQuestion: " . $e->getMessage());
        }
        
        return "Je n'ai pas trouvé de projet correspondant à \"" . htmlspecialchars($projectName) . "\". 😔\n\nEssayez de me demander la liste de tous les projets pour voir ceux disponibles !";
    }

    private function handleCategoryQuestion($userMessage) {
        try {
            $projects = $this->projectModel->getAllProjects();
            $categories = [];
            
            foreach ($projects as $project) {
                if (!empty($project['category'])) {
                    $cat = $project['category'];
                    if (!isset($categories[$cat])) {
                        $categories[$cat] = 0;
                    }
                    $categories[$cat]++;
                }
            }
            
            if (empty($categories)) {
                return "Aucune catégorie disponible pour le moment.";
            }
            
            $response = "📁 **Catégories de projets disponibles** :\n\n";
            foreach ($categories as $category => $count) {
                $response .= "• **" . htmlspecialchars($category) . "** : " . $count . " projet(s)\n";
            }
            
            return $response;
        } catch (Exception $e) {
            error_log("Error in handleCategoryQuestion: " . $e->getMessage());
            return "Désolé, je ne peux pas récupérer les catégories pour le moment. 🔧";
        }
    }

    private function extractProjectName($message, $originalMessage) {
        // Try to extract project name from common patterns
        $patterns = [
            '/\b(projet|project)\s+["\']?([^"\'\n\r]+)["\']?/i',
            '/["\']([^"\'\n\r]+)["\']/',
            '/\b(sur|de|à propos|au sujet)\s+["\']?([^"\'\n\r?]+)["\']?/i',
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $originalMessage, $matches)) {
                $name = trim($matches[count($matches) - 1]);
                if (strlen($name) > 2) {
                    return $name;
                }
            }
        }
        
        // Fallback: try to find capitalized words (likely project names)
        if (preg_match('/\b([A-Z][a-z]+(?:\s+[A-Z][a-z]+)*)\b/', $originalMessage, $matches)) {
            return $matches[1];
        }
        
        return '';
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
}




