<?php
require_once "models/Event.php";
require_once "config/database.php";

class EventChatbotController {
    
    private $eventModel;
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->eventModel = new Event($this->conn);
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
            error_log("EventChatbot error: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => 'An error occurred processing your request.'
            ]);
        }
    }

    /**
     * Get a response based on real database content for events
     */
    public function getResponse($userMessage) {
        $message = strtolower(trim($userMessage));
        $originalMessage = $userMessage;
        
        error_log("EventChatbot - User message: " . $userMessage);
        
        // 1. Basic Greetings
        if (preg_match('/\b(hello|hi|bonjour|salut|coucou|hey|yo)\b/i', $userMessage)) {
            return "Bonjour ! 👋 Je suis **E-Bot**, votre assistant intelligent pour les événements ! 📅\n\nJe peux vous aider à :\n- 📅 Trouver des événements par nom ou description\n- 🕐 Vous informer sur les dates de début et de fin\n- 👥 Répondre à vos questions sur les participations\n- 📚 Répondre à vos questions sur nos événements\n\nQue souhaitez-vous savoir ?";
        }
        
        // 2. Questions about "what events do you have" or "list all events"
        if (preg_match('/\b(quels|quelles|liste|tous|tout|combien|nombre|total)\b.*\b(événements|events|événement|event)\b/i', $userMessage) || 
            preg_match('/\b(événements|events|événement|event)\b.*\b(quels|quelles|liste|tous|tout|combien|nombre|total|avez|disponible|avoir)\b/i', $userMessage) ||
            preg_match('/\b(qu\'est|qu\'y|y a|il y a)\b.*\b(événements|events|événement|event)\b/i', $userMessage)) {
            return $this->getAllEventsResponse();
        }

        // 3. Questions about dates (start, end, when)
        if (preg_match('/\b(quand|date|début|démarre|commence|start|fin|finit|termine|end|durée|duration)\b/i', $userMessage)) {
            return $this->handleDateQuestion($userMessage);
        }

        // 4. Questions about participation
        if (preg_match('/\b(participer|participation|inscrire|inscription|s\'inscrire|s\'inscrire)\b/i', $userMessage)) {
            return $this->handleParticipationQuestion($userMessage);
        }

        // 5. Extract Keywords
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
            if (preg_match('/\b(quel|quelle|quels|quelles|combien|où|comment|pourquoi)\b/i', $userMessage)) {
                $keywords = array_filter(explode(' ', $cleanMessage), function($w) use ($stopWords) {
                    return !in_array($w, $stopWords) && strlen($w) > 2;
                });
            }
            if (empty($keywords)) {
                $keywords = [preg_replace('/[^\p{L}\p{N}]/u', '', $userMessage)];
            }
        }
        
        error_log("EventChatbot - Extracted keywords: " . implode(', ', $keywords));

        // 6. Handle specific event questions
        if (preg_match('/\b(parle|dis|raconte|explique|décris|informe|donne|montre)\b.*\b(sur|de|à propos|au sujet)\b/i', $userMessage) ||
            preg_match('/\b(qu\'est|qu\'est-ce|what is|what\'s|c\'est quoi|qu\'est-ce que c\'est)\b/i', $userMessage) ||
            preg_match('/\b(à propos|au sujet|sur|de|informations|info|détails|détail)\b.*\b(événement|event)\b/i', $userMessage)) {
            return $this->handleSpecificEventQuestion($userMessage, $originalMessage);
        }

        // 7. Fetch Real Data from Database
        try {
            $events = $this->eventModel->getAllEvents();
            error_log("EventChatbot - Found " . count($events) . " events in database");
        } catch (Exception $e) {
            error_log("Error fetching events: " . $e->getMessage());
            $events = [];
        }

        // 8. Search for matches in Events
        $foundItems = [];
        $searchString = implode(' ', $keywords);
        
        foreach ($events as $event) {
            $score = 0;
            $title = strtolower($event['nom_evenet'] ?? '');
            $description = strtolower($event['description'] ?? '');
            
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
            }
            
            if ($score >= 5) {
                $event['score'] = $score;
                $foundItems[] = $event;
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
                return $this->getDetailedEventInfo($foundItems[0]);
            }
            
            $response = "🎯 J'ai trouvé **" . $totalCount . " événement(s)** correspondant à votre recherche :\n\n";
            
            $count = 0;
            foreach ($foundItems as $event) {
                if ($count >= 5) break;
                
                $dateInfo = $this->getEventDateInfo($event);
                
                $response .= "📅 **" . htmlspecialchars($event['nom_evenet'] ?? 'Sans nom') . "**\n";
                $response .= $dateInfo . "\n";
                
                $desc = !empty($event['description']) ? strip_tags($event['description']) : 'Aucune description disponible';
                $desc = mb_substr($desc, 0, 120, 'UTF-8');
                if (mb_strlen($event['description'] ?? '', 'UTF-8') > 120) $desc .= "...";
                $response .= "📝 " . $desc . "\n\n";
                
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
                $response .= $dateInfo . "\n\n";
            }
            
            $response .= "💡 Demandez-moi plus d'informations sur un événement spécifique !";
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

    private function handleParticipationQuestion($userMessage) {
        return "👥 **Pour participer à un événement** :\n\n" .
               "1. 📅 Consultez la liste des événements disponibles\n" .
               "2. 🎯 Choisissez l'événement qui vous intéresse\n" .
               "3. ➕ Cliquez sur le bouton \"Participer\"\n" .
               "4. ✅ Votre participation sera enregistrée !\n\n" .
               "💡 Vous devez être connecté pour participer à un événement.\n" .
               "📋 Vous pouvez voir vos participations dans \"Mes Participations\".";
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
                $title = strtolower($event['nom_evenet'] ?? '');
                $score = 0;
                
                if ($title === strtolower($eventName)) {
                    $score = 100;
                } elseif (strpos($title, strtolower($eventName)) !== false) {
                    $score = 50;
                }
                
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestMatch = $event;
                }
            }
            
            if ($bestMatch && $bestScore >= 30) {
                return $this->getDetailedEventInfo($bestMatch);
            }
        } catch (Exception $e) {
            error_log("Error in handleSpecificEventQuestion: " . $e->getMessage());
        }
        
        return "Je n'ai pas trouvé d'événement correspondant à \"" . htmlspecialchars($eventName) . "\". 😔\n\nEssayez de me demander la liste de tous les événements pour voir ceux disponibles !";
    }

    private function extractEventName($message, $originalMessage) {
        // Try to extract event name from common patterns
        $patterns = [
            '/\b(événement|event)\s+["\']?([^"\'\n\r]+)["\']?/i',
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
        
        // Fallback: try to find capitalized words (likely event names)
        if (preg_match('/\b([A-Z][a-z]+(?:\s+[A-Z][a-z]+)*)\b/', $originalMessage, $matches)) {
            return $matches[1];
        }
        
        return '';
    }

    private function getDetailedEventInfo($event) {
        $dateInfo = $this->getEventDateInfo($event);
        
        $response = "📅 **" . htmlspecialchars($event['nom_evenet'] ?? 'Sans nom') . "**\n\n";
        
        $response .= "🕐 **Dates et Horaires** :\n";
        $response .= $dateInfo . "\n\n";
        
        if (!empty($event['description'])) {
            $desc = strip_tags($event['description']);
            $response .= "📝 **Description** :\n" . $desc . "\n\n";
        }
        
        $response .= "💡 Vous pouvez participer à cet événement en cliquant sur le bouton \"Participer\" !";
        
        return $response;
    }

    private function getEventDateInfo($event) {
        // Handle both new structure (date_debut, date_fin) and old structure (dateevent, duree)
        if (isset($event['date_debut']) && isset($event['date_fin'])) {
            try {
                $date_debut = new DateTime($event['date_debut']);
                $date_fin = new DateTime($event['date_fin']);
                
                $now = new DateTime();
                $isPast = $date_fin < $now;
                $isUpcoming = $date_debut > $now;
                $isOngoing = $date_debut <= $now && $date_fin >= $now;
                
                $status = $isPast ? '🔴 Passé' : ($isOngoing ? '🟢 En cours' : '🟡 À venir');
                
                $response = "   " . $status . "\n";
                $response .= "   🕐 Début : " . $date_debut->format('d/m/Y à H:i') . "\n";
                $response .= "   🕐 Fin : " . $date_fin->format('d/m/Y à H:i') . "\n";
                
                // Calculate duration
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
        
        // Fallback to old structure
        if (isset($event['dateevent'])) {
            try {
                $date = new DateTime($event['dateevent']);
                $duree = $event['duree'] ?? '00:00:00';
                
                $response = "   📅 Date : " . $date->format('d/m/Y') . "\n";
                $response .= "   ⏱️ Durée : " . $duree;
                
                return $response;
            } catch (Exception $e) {
                error_log("Error parsing old date structure: " . $e->getMessage());
            }
        }
        
        return "   📅 Informations de date non disponibles";
    }
}




