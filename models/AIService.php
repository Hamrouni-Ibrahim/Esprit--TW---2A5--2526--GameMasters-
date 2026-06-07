<?php
require_once "models/Formation.php";
require_once "models/Education.php";

class AIService {
    
    private $formationModel;
    private $educationModel;

    public function __construct() {
        $this->formationModel = new Formation();
        $this->educationModel = new Education();
    }

    /**
     * Get a response based on real database content
     * 
     * @param string $userMessage The user's message
     * @return string The AI's response
     */
    public function getResponse($userMessage) {
        // Simulate network delay
        sleep(1);
        
        $message = strtolower($userMessage);
        
        // 1. Basic Greetings
        if (preg_match('/\b(hello|hi|bonjour|salut|coucou)\b/', $message)) {
            return "Bonjour ! Je suis votre assistant virtuel intelligent. Je connais tout le contenu de ce site. Posez-moi une question sur nos formations !";
        }

        // 2. Extract Keywords (Simple NLP)
        $stopWords = ['je', 'tu', 'il', 'elle', 'nous', 'vous', 'ils', 'elles', 'le', 'la', 'les', 'un', 'une', 'des', 'de', 'du', 'au', 'aux', 'et', 'ou', 'mais', 'donc', 'or', 'ni', 'car', 'ce', 'cet', 'cette', 'ces', 'mon', 'ton', 'son', 'ma', 'ta', 'sa', 'mes', 'tes', 'ses', 'notre', 'votre', 'leur', 'nos', 'vos', 'leurs', 'qui', 'que', 'quoi', 'dont', 'où', 'quand', 'comment', 'pourquoi', 'quel', 'quelle', 'quels', 'quelles', 'est', 'sont', 'a', 'ont', 'veux', 'voudrais', 'souhaite', 'aimerais', 'apprendre', 'chercher', 'trouver', 'avoir', 'besoin', 'aide', 'sur', 'dans', 'par', 'pour', 'vers', 'avec', 'sans', 'sous', 'education', 'formation', 'cours'];
        
        // Remove punctuation
        $cleanMessage = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $message);
        $words = explode(' ', $cleanMessage);
        
        $keywords = [];
        foreach ($words as $word) {
            $word = trim($word);
            if (!empty($word) && !in_array($word, $stopWords) && strlen($word) > 2) {
                $keywords[] = $word;
            }
        }

        // If no keywords found, try to match the whole sentence loosely
        if (empty($keywords)) {
            $keywords[] = $message;
        }

        // 3. Fetch Real Data
        $formations = $this->formationModel->getAll()->fetchAll(PDO::FETCH_ASSOC);
        
        // 5. Search for matches in Formations
        $foundItems = [];
        $formationIdsFound = []; // Track found formations to filter children later
        
        // Search Formations
        foreach ($formations as $formation) {
            $score = 0;
            $title = strtolower($formation['title']);
            $description = strtolower($formation['description']);
            
            foreach ($keywords as $keyword) {
                if (strpos($title, $keyword) !== false) $score += 10;
                if (strpos($description, $keyword) !== false) $score += 2;
            }
            
            // Threshold: Score must be at least 5 (e.g., title match or multiple desc matches)
            if ($score >= 5) {
                $formation['score'] = $score;
                $formation['type'] = 'formation';
                $foundItems[] = $formation;
                $formationIdsFound[$formation['id']] = $score;
            }
        }

        // Search Educations
        $educations = $this->educationModel->getAll()->fetchAll(PDO::FETCH_ASSOC);
        foreach ($educations as $education) {
            $score = 0;
            $title = strtolower($education['title']);
            $description = strtolower($education['description']);
            
            foreach ($keywords as $keyword) {
                if (strpos($title, $keyword) !== false) $score += 10;
                if (strpos($description, $keyword) !== false) $score += 2;
            }
            
            // Threshold: Score must be at least 5
            if ($score >= 5) {
                // Smart Filter:
                // If the parent formation is already found with a HIGHER or EQUAL score, 
                // and this education doesn't have a very high score (meaning it's not a specific title match),
                // we might want to hide it to avoid clutter.
                // BUT, if the user searched for "PDO" (Education) and "PHP" (Formation), both might have high scores.
                // Let's just rely on the threshold for now, but ensure we don't show weak education matches if the parent is there.
                
                $parentId = $education['formation_id'] ?? 0;
                if (isset($formationIdsFound[$parentId])) {
                    // If parent is found...
                    // If education score is significantly lower than parent, skip it (it's likely just inheriting keywords)
                    if ($score < $formationIdsFound[$parentId]) {
                        continue;
                    }
                }

                $education['score'] = $score;
                $education['type'] = 'education';
                
                // Get parent formation title
                if (!empty($education['formation_id'])) {
                    $parent = $this->formationModel->getById($education['formation_id']);
                    $education['parent_formation'] = $parent ? $parent['title'] : 'Formation inconnue';
                } else {
                    $education['parent_formation'] = 'Aucune formation';
                }
                
                $foundItems[] = $education;
            }
        }

        // Sort by score
        usort($foundItems, function($a, $b) {
            return $b['score'] - $a['score'];
        });

        // 6. Construct Response based on findings
        if (!empty($foundItems)) {
            $response = "J'ai trouvé " . count($foundItems) . " résultat(s) correspondant à votre recherche :\n\n";
            
            // Limit to 3 results
            $count = 0;
            foreach ($foundItems as $item) {
                if ($count >= 3) break;
                
                if ($item['type'] === 'formation') {
                    $response .= "🎓 **Formation : " . $item['title'] . "**\n";
                    $link = "?controller=formation&action=detail&id=" . $item['id'];
                } else {
                    $response .= "📘 **Éducation : " . $item['title'] . "**\n";
                    $response .= "   *(Dans la formation : " . $item['parent_formation'] . ")*\n";
                    $link = "?controller=education&action=detail&id=" . $item['id'];
                }
                
                // Add a short snippet of description
                $desc = substr(strip_tags($item['description']), 0, 100) . "...";
                $response .= $desc . "\n";
                // Add a link
                $response .= "[Voir les détails](" . $link . ")\n\n";
                $count++;
            }
            
            if (count($foundItems) > 3) {
                $response .= "...et d'autres encore !";
            }
            
            return $response;
        }
        
        // 6. Fallback
        return "Je n'ai pas trouvé de résultat pertinent pour '" . implode(" ", $keywords) . "'. Essayez des termes plus précis.";
    }
}
?>
