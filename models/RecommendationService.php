<?php
require_once "models/Formation.php";

class RecommendationService {
    
    private $educationModel;
    private $categoryModel;
    private $categoryCache = []; // Cache category names for performance

    public function __construct() {
        $this->formationModel = new Formation();
        require_once "models/Education.php";
        require_once "models/Category.php";
        $this->educationModel = new Education();
        $this->categoryModel = new Category();
    }

    /**
     * Get recommended formations AND educations based on a source formation
     * Uses multi-criteria scoring: skills overlap, category, difficulty, text similarity
     * 
     * @param array $currentFormation The formation currently being viewed
     * @param int $limit Number of recommendations to return
     * @return array List of recommended items sorted by relevance score
     */
    public function getRecommendations($currentFormation, $limit = 5) {
        $recommendations = [];
        
        if (!$currentFormation || empty($currentFormation['id'])) {
            return [];
        }
        
        // Get current formation's category - try all possible ways
        $currentCategoryId = null;
        if (isset($currentFormation['category_id']) && !empty($currentFormation['category_id'])) {
            $currentCategoryId = (int)$currentFormation['category_id'];
        }
        
        $currentCategoryString = null;
        if (isset($currentFormation['categorie']) && $currentFormation['categorie'] !== null) {
            $catValue = trim((string)$currentFormation['categorie']);
            if (!empty($catValue)) {
                $currentCategoryString = strtolower($catValue);
            }
        }
        
        $currentCategoryName = $this->getCategoryName($currentFormation);
        if (!empty($currentCategoryName)) {
            $currentCategoryName = trim(strtolower($currentCategoryName));
        } else {
            $currentCategoryName = null;
        }
        
        // Extract data from current formation for scoring
        $currentSkills = $this->extractSkills($currentFormation['competences'] ?? '');
        $currentKeywords = $this->extractKeywords($currentFormation['title'] . ' ' . ($currentFormation['description'] ?? ''));
        $currentDifficulty = isset($currentFormation['difficulte']) ? strtolower(trim($currentFormation['difficulte'])) : null;
        
        // 1. Process Formations with scoring
        $allFormations = $this->formationModel->getAll()->fetchAll(PDO::FETCH_ASSOC);
        foreach ($allFormations as $formation) {
            // Skip the current formation
            if (isset($formation['id']) && $formation['id'] == $currentFormation['id']) {
                continue;
            }
            
            $score = $this->calculateRecommendationScore(
                $formation,
                $currentFormation,
                $currentSkills,
                $currentKeywords,
                $currentCategoryId,
                $currentCategoryString,
                $currentCategoryName,
                $currentDifficulty
            );
            
            if ($score > 0) {
                $formation['recommendation_score'] = $score;
                $formation['type'] = 'formation';
                $recommendations[] = $formation;
            }
        }

        // 2. Process Educations with scoring
        $allEducations = $this->educationModel->getAll()->fetchAll(PDO::FETCH_ASSOC);
        foreach ($allEducations as $education) {
            // Skip educations that belong to the current formation
            if (isset($education['formation_id']) && $education['formation_id'] == $currentFormation['id']) {
                continue;
            }
            
            $score = $this->calculateRecommendationScore(
                $education,
                $currentFormation,
                $currentSkills,
                $currentKeywords,
                $currentCategoryId,
                $currentCategoryString,
                $currentCategoryName,
                $currentDifficulty
            );
            
            if ($score > 0) {
                $education['recommendation_score'] = $score;
                $education['type'] = 'education';
                $recommendations[] = $education;
            }
        }
        
        // Sort by recommendation score (highest first), then by type, then by date
        usort($recommendations, function($a, $b) {
            $aScore = $a['recommendation_score'] ?? 0;
            $bScore = $b['recommendation_score'] ?? 0;
            
            // Sort by score first (highest first)
            if ($bScore !== $aScore) {
                return $bScore - $aScore;
            }
            
            // If scores equal, prioritize formations
            $typeOrder = ['formation' => 0, 'education' => 1];
            $aType = $typeOrder[$a['type']] ?? 2;
            $bType = $typeOrder[$b['type']] ?? 2;
            if ($aType !== $bType) {
                return $aType - $bType;
            }
            
            // Finally by creation date (newest first)
            $aDate = isset($a['created_at']) ? strtotime($a['created_at']) : 0;
            $bDate = isset($b['created_at']) ? strtotime($b['created_at']) : 0;
            return $bDate - $aDate;
        });
        
        return array_slice($recommendations, 0, $limit);
    }

    /**
     * Calculate recommendation score for an item based on multiple criteria
     * Higher score = better recommendation
     * 
     * Scoring weights:
     * - Skills/Competences overlap: 50 points (highest - most important)
     * - Category match: 30 points
     * - Difficulty compatibility: 15 points
     * - Keyword/text similarity: 5 points
     */
    private function calculateRecommendationScore($item, $currentFormation, $currentSkills, $currentKeywords, $currentCategoryId, $currentCategoryString, $currentCategoryName, $currentDifficulty) {
        $score = 0;
        
        // 1. SKILLS/COMPETENCES OVERLAP (50 points max - Highest Priority)
        if (!empty($currentSkills)) {
            $itemSkills = $this->extractSkills($item['competences'] ?? '');
            if (!empty($itemSkills)) {
                $commonSkills = array_intersect($currentSkills, $itemSkills);
                $totalSkills = count(array_unique(array_merge($currentSkills, $itemSkills)));
                if ($totalSkills > 0) {
                    $skillOverlapRatio = count($commonSkills) / $totalSkills;
                    $score += (int)($skillOverlapRatio * 50); // Up to 50 points
                }
            }
        }
        
        // 2. CATEGORY MATCH (30 points max)
        $categoryScore = 0;
        $itemCategoryString = null;
        if (isset($item['categorie']) && $item['categorie'] !== null) {
            $catValue = trim((string)$item['categorie']);
            if (!empty($catValue)) {
                $itemCategoryString = strtolower($catValue);
            }
        }
        
        // Category ID match (20 points)
        if ($currentCategoryId && isset($item['category_id']) && !empty($item['category_id'])) {
            if ((int)$item['category_id'] === $currentCategoryId) {
                $categoryScore = 20;
            }
        }
        
        // Category string match (15 points) - only if ID didn't match
        if ($categoryScore === 0 && $currentCategoryString && $itemCategoryString) {
            if ($itemCategoryString === $currentCategoryString) {
                $categoryScore = 15;
            } elseif (stripos($itemCategoryString, $currentCategoryString) !== false || 
                     stripos($currentCategoryString, $itemCategoryString) !== false) {
                $categoryScore = 8; // Partial match
            }
        }
        
        // Category name from DB match (15 points) - only if no other match
        if ($categoryScore === 0 && $currentCategoryName) {
            $itemCategoryName = $this->getCategoryName($item);
            if (!empty($itemCategoryName)) {
                $itemCategoryName = trim(strtolower($itemCategoryName));
                if ($itemCategoryName === $currentCategoryName) {
                    $categoryScore = 15;
                }
            }
        }
        $score += $categoryScore;
        
        // 3. DIFFICULTY COMPATIBILITY (15 points max)
        if ($currentDifficulty && isset($item['difficulte']) && !empty($item['difficulte'])) {
            $itemDifficulty = strtolower(trim($item['difficulte']));
            $difficultyLevels = ['débutant', 'intermédiaire', 'avancé', 'expert'];
            $currentIndex = array_search($currentDifficulty, $difficultyLevels);
            $itemIndex = array_search($itemDifficulty, $difficultyLevels);
            
            if ($currentIndex !== false && $itemIndex !== false) {
                if ($itemIndex === $currentIndex) {
                    $score += 15; // Same difficulty
                } elseif (abs($itemIndex - $currentIndex) === 1) {
                    $score += 8; // Adjacent difficulty (good progression)
                } elseif (abs($itemIndex - $currentIndex) === 2) {
                    $score += 3; // Two levels apart
                }
            }
        }
        
        // 4. KEYWORD/TEXT SIMILARITY (5 points max)
        $itemKeywords = $this->extractKeywords($item['title'] . ' ' . ($item['description'] ?? ''));
        if (!empty($currentKeywords) && !empty($itemKeywords)) {
            $commonKeywords = array_intersect($currentKeywords, $itemKeywords);
            $score += min(count($commonKeywords) * 1, 5); // 1 point per keyword, max 5
        }
        
        return $score;
    }
    
    /**
     * Extract skills from competences field (comma-separated or space-separated)
     */
    private function extractSkills($competences) {
        if (empty($competences)) {
            return [];
        }
        
        // Handle comma-separated, semicolon-separated, or space-separated
        $skills = preg_split('/[,;\s]+/', $competences);
        $skills = array_map('trim', $skills);
        $skills = array_map('strtolower', $skills);
        $skills = array_filter($skills, function($skill) {
            return !empty($skill) && strlen($skill) > 1;
        });
        
        return array_unique($skills);
    }
    
    /**
     * Calculate match score for an item (old method - kept for backward compatibility)
     */
    private function calculateScore($item, $currentFormation, $keywords) {
        $score = 0;
        
        // 1. Category Name Match (Highest Priority - 40 points)
        $currentCategoryName = $this->getCategoryName($currentFormation);
        $itemCategoryName = $this->getCategoryName($item);
        
        if (!empty($currentCategoryName) && !empty($itemCategoryName)) {
            // Exact match (case-insensitive)
            if (strtolower(trim($currentCategoryName)) === strtolower(trim($itemCategoryName))) {
                $score += 40;
            }
            // Partial match (contains)
            elseif (stripos($itemCategoryName, $currentCategoryName) !== false || 
                    stripos($currentCategoryName, $itemCategoryName) !== false) {
                $score += 25;
            }
        }
        
        // Fallback: Category ID match (if category names not available)
        if ($score == 0 && isset($item['category_id']) && isset($currentFormation['category_id']) && 
            !empty($item['category_id']) && !empty($currentFormation['category_id'])) {
            if ($item['category_id'] == $currentFormation['category_id']) {
                $score += 30;
            }
        }
        
        // Fallback: String category match (if IDs not available)
        if ($score == 0 && isset($item['categorie']) && isset($currentFormation['categorie'])) {
            $itemCat = trim(strtolower($item['categorie']));
            $currentCat = trim(strtolower($currentFormation['categorie']));
            if ($itemCat === $currentCat) {
                $score += 25;
            } elseif (!empty($itemCat) && !empty($currentCat) && 
                     (stripos($itemCat, $currentCat) !== false || stripos($currentCat, $itemCat) !== false)) {
                $score += 15;
            }
        }
        
        // 2. Difficulty Match (High Priority - 30 points)
        if (isset($item['difficulte']) && isset($currentFormation['difficulte']) && 
            !empty($item['difficulte']) && !empty($currentFormation['difficulte'])) {
            
            $itemDiff = strtolower(trim($item['difficulte']));
            $currentDiff = strtolower(trim($currentFormation['difficulte']));
            
            // Exact difficulty match
            if ($itemDiff === $currentDiff) {
                $score += 30;
            }
            // Similar difficulty levels (adjacent levels)
            else {
                $difficultyLevels = ['débutant', 'intermédiaire', 'avancé', 'expert'];
                $itemIndex = array_search($itemDiff, $difficultyLevels);
                $currentIndex = array_search($currentDiff, $difficultyLevels);
                
                if ($itemIndex !== false && $currentIndex !== false) {
                    $diff = abs($itemIndex - $currentIndex);
                    if ($diff == 1) {
                        $score += 15; // Adjacent difficulty
                    } elseif ($diff == 2) {
                        $score += 5; // Two levels apart
                    }
                }
            }
        }
        
        // 3. Keyword Overlap (Lower Priority - 2 points per keyword)
        $targetKeywords = $this->extractKeywords($item['title'] . ' ' . ($item['description'] ?? ''));
        $commonKeywords = array_intersect($keywords, $targetKeywords);
        $score += count($commonKeywords) * 2;

        return $score;
    }
    
    /**
     * Get category name from item (formation or education)
     * Uses cache for performance
     */
    private function getCategoryName($item) {
        // Try to get from category_id first
        if (isset($item['category_id']) && !empty($item['category_id'])) {
            // Check cache first
            if (isset($this->categoryCache[$item['category_id']])) {
                return $this->categoryCache[$item['category_id']];
            }
            
            // Fetch from database
            $category = $this->categoryModel->getById($item['category_id']);
            if ($category && isset($category['nom'])) {
                $this->categoryCache[$item['category_id']] = $category['nom'];
                return $category['nom'];
            }
        }
        
        // Fallback to categorie string field
        if (isset($item['categorie']) && !empty($item['categorie'])) {
            return $item['categorie'];
        }
        
        return null;
    }
    
    /**
     * Extract significant keywords from text
     */
    private function extractKeywords($text) {
        $text = strtolower($text);
        // Remove punctuation
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);
        
        $words = explode(' ', $text);
        $stopWords = ['je', 'tu', 'il', 'elle', 'nous', 'vous', 'ils', 'elles', 'le', 'la', 'les', 'un', 'une', 'des', 'de', 'du', 'au', 'aux', 'et', 'ou', 'mais', 'donc', 'or', 'ni', 'car', 'ce', 'cet', 'cette', 'ces', 'mon', 'ton', 'son', 'ma', 'ta', 'sa', 'mes', 'tes', 'ses', 'notre', 'votre', 'leur', 'nos', 'vos', 'leurs', 'qui', 'que', 'quoi', 'dont', 'où', 'quand', 'comment', 'pourquoi', 'quel', 'quelle', 'quels', 'quelles', 'est', 'sont', 'a', 'ont', 'sur', 'dans', 'par', 'pour', 'vers', 'avec', 'sans', 'sous', 'formation', 'cours', 'apprendre'];
        
        $keywords = [];
        foreach ($words as $word) {
            $word = trim($word);
            if (!empty($word) && !in_array($word, $stopWords) && strlen($word) > 2) {
                $keywords[] = $word;
            }
        }
        
        return array_unique($keywords);
    }
}
?>
