<?php
require_once __DIR__ . '/../models/Project.php';

class ProjectController {
    private $model;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->model = new Project($db);
    }

    public function index() {
        $sort = $_GET['sort'] ?? 'date_desc';
        $search = $_GET['search'] ?? '';
        
        if (!empty($search)) {
            $projects = $this->model->searchProjects($search, $sort);
        } else {
            $projects = $this->model->getAllProjects($sort);
        }
        
        $pageTitle = 'Nos Projets - Game Master';
        $currentPage = 'projects';
        require __DIR__ . '/../views/front/includes/header.php';
        require __DIR__ . '/../views/front/projects/index.php';
        require __DIR__ . '/../views/front/includes/footer.php';
    }

    public function details() {
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            header("Location: ?action=projects");
            exit;
        }
        
        $project = $this->model->getProjectById($id);
        
        if (!$project) {
            header("Location: ?action=projects");
            exit;
        }
        
        $pageTitle = htmlspecialchars($project['title']) . ' - Game Master';
        $currentPage = 'projects';
        require __DIR__ . '/../views/front/includes/header.php';
        require __DIR__ . '/../views/front/projects/details.php';
        require __DIR__ . '/../views/front/includes/footer.php';
    }

    // Admin methods
    public function adminList() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: ?action=login");
            exit;
        }
        
        $sort = $_GET['sort'] ?? 'date_desc';
        $search = $_GET['search'] ?? '';
        
        // Initialize variables
        $projects = [];
        $projectStats = [
            'total_projects' => 0,
            'total_categories' => 0,
            'projects_per_category' => []
        ];
        
        try {
            if (!empty($search)) {
                $projects = $this->model->searchProjects($search, $sort);
            } else {
                $projects = $this->model->getAllProjects($sort);
            }
            
            $projectStats = $this->model->getStatistics();
        } catch (Exception $e) {
            error_log("Admin projects error: " . $e->getMessage());
            $_SESSION['admin_error'] = "Erreur lors du chargement des projets. Vérifiez que la table 'projects' existe dans la base de données.";
        }
        
        $pageTitle = 'Gestion des Projets - Game Master';
        $currentPage = 'admin_projects';
        require __DIR__ . '/../views/admin/includes/header.php';
        require __DIR__ . '/../views/admin/projects.php';
        require __DIR__ . '/../views/admin/includes/footer.php';
    }

    public function adminAdd() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: ?action=login");
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $category = $_POST['category'] ?? '';
            $description = $_POST['description'] ?? '';
            
            // Handle image upload
            $imagePath = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imagePath = $this->handleImageUpload($_FILES['image'], $title);
                if ($imagePath === false) {
                    $_SESSION['admin_error'] = "Erreur lors de l'upload de l'image. Veuillez vérifier le format (JPG, PNG, GIF, WebP) et la taille (max 5MB).";
                    header("Location: ?action=admin_projects");
                    exit();
                }
            } else {
                $_SESSION['admin_error'] = "Une image est requise.";
                header("Location: ?action=admin_projects");
                exit();
            }
            
            if (!empty($title) && !empty($description) && !empty($imagePath)) {
                $result = $this->model->addProject($title, $category, $imagePath, $description);
                if ($result) {
                    $_SESSION['admin_success'] = "Projet ajouté avec succès !";
                } else {
                    $_SESSION['admin_error'] = "Erreur lors de l'ajout du projet.";
                }
            } else {
                $_SESSION['admin_error'] = "Veuillez remplir tous les champs requis.";
            }
            
            header("Location: ?action=admin_projects");
            exit;
        }
    }

    public function adminEdit() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: ?action=login");
            exit;
        }
        
        $id = $_GET['id'] ?? null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $title = $_POST['title'] ?? '';
            $category = $_POST['category'] ?? '';
            $description = $_POST['description'] ?? '';
            
            // Get existing project to preserve image
            $existingProject = $this->model->getProjectById($id);
            $imagePath = $existingProject['image'] ?? ''; // Keep existing image by default
            
            // Handle image upload - if new image uploaded, use it; otherwise keep existing
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $newImagePath = $this->handleImageUpload($_FILES['image'], $title);
                if ($newImagePath !== false) {
                    // Delete old image if it exists and is in uploads directory
                    if (!empty($existingProject['image']) && strpos($existingProject['image'], 'public/uploads/projects/') === 0) {
                        $oldImagePath = __DIR__ . '/../' . $existingProject['image'];
                        if (file_exists($oldImagePath)) {
                            @unlink($oldImagePath);
                        }
                    }
                    $imagePath = $newImagePath;
                } else {
                    $_SESSION['admin_error'] = "Erreur lors de l'upload de l'image. Veuillez vérifier le format (JPG, PNG, GIF, WebP) et la taille (max 5MB).";
                    header("Location: ?action=admin_projects");
                    exit();
                }
            }
            
            if ($id && !empty($title) && !empty($description)) {
                $result = $this->model->updateProject($id, $title, $category, $imagePath, $description);
                if ($result) {
                    $_SESSION['admin_success'] = "Projet modifié avec succès !";
                } else {
                    $_SESSION['admin_error'] = "Erreur lors de la modification.";
                }
            } else {
                $_SESSION['admin_error'] = "Veuillez remplir tous les champs requis.";
            }
            
            header("Location: ?action=admin_projects");
            exit;
        }
        
        if ($id) {
            $project = $this->model->getProjectById($id);
            if (!$project) {
                header("Location: ?action=admin_projects");
                exit;
            }
            
            $pageTitle = 'Modifier le Projet - Game Master';
            $currentPage = 'admin_projects';
            require __DIR__ . '/../views/admin/includes/header.php';
            require __DIR__ . '/../views/admin/project_edit.php';
            require __DIR__ . '/../views/admin/includes/footer.php';
        } else {
            header("Location: ?action=admin_projects");
            exit;
        }
    }

    public function adminDelete() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: ?action=login");
            exit;
        }
        
        $id = $_GET['id'] ?? null;
        
        if ($id) {
            // Get project to delete image file
            $project = $this->model->getProjectById($id);
            
            $result = $this->model->deleteProject($id);
            if ($result) {
                // Delete image file if it exists in uploads directory
                if (!empty($project['image']) && strpos($project['image'], 'public/uploads/projects/') === 0) {
                    $imagePath = __DIR__ . '/../' . $project['image'];
                    if (file_exists($imagePath)) {
                        @unlink($imagePath);
                    }
                }
                $_SESSION['admin_success'] = "Projet supprimé avec succès !";
            } else {
                $_SESSION['admin_error'] = "Erreur lors de la suppression.";
            }
        }
        
        header("Location: ?action=admin_projects");
        exit;
    }
    
    private function handleImageUpload($imageFile, $projectTitle) {
        if (!$imageFile || $imageFile['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($imageFile['type'], $allowedTypes)) {
            return false;
        }

        if ($imageFile['size'] > 5 * 1024 * 1024) { // 5MB max
            return false;
        }

        // Create upload directory if it doesn't exist
        $uploadDir = __DIR__ . '/../public/uploads/projects/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = pathinfo($imageFile['name'], PATHINFO_EXTENSION);
        $safeName = preg_replace('/[^a-zA-Z0-9]/', '_', $projectTitle);
        $filename = 'project_' . $safeName . '_' . uniqid() . '.' . $extension;
        $filePath = $uploadDir . $filename;

        if (move_uploaded_file($imageFile['tmp_name'], $filePath)) {
            // Return relative path from project root
            return 'public/uploads/projects/' . $filename;
        }

        return false;
    }

    public function generateAISummary() {
        // Disable error output to prevent breaking JSON
        ini_set('display_errors', 0);
        error_reporting(E_ALL);
        
        // Set headers first - before any output
        header('Content-Type: application/json; charset=utf-8');
        
        // Prevent any output before JSON
        ob_start();
        
        try {
            // Get JSON input
            $rawInput = file_get_contents('php://input');
            
            if (empty($rawInput)) {
                ob_end_clean();
                echo json_encode([
                    'success' => false,
                    'error' => 'Aucune donnée reçue'
                ]);
                exit;
            }
            
            $input = json_decode($rawInput, true);
            
            // Check if JSON was parsed correctly
            if (json_last_error() !== JSON_ERROR_NONE) {
                ob_end_clean();
                error_log("JSON decode error: " . json_last_error_msg() . " | Raw input: " . substr($rawInput, 0, 200));
                echo json_encode([
                    'success' => false,
                    'error' => 'Format de données invalide: ' . json_last_error_msg()
                ]);
                exit;
            }
            
            if (!isset($input['project_id']) || !isset($input['title']) || !isset($input['description'])) {
                ob_end_clean();
                echo json_encode([
                    'success' => false,
                    'error' => 'Données manquantes: project_id, title ou description'
                ]);
                exit;
            }
            
            $projectId = $input['project_id'];
            $title = $input['title'];
            $description = $input['description'];
            
            // Validate inputs
            if (empty($title) || empty($description)) {
                ob_end_clean();
                echo json_encode([
                    'success' => false,
                    'error' => 'Le titre et la description ne peuvent pas être vides'
                ]);
                exit;
            }
            
            // Generate AI summary
            $summary = $this->generateSummaryWithAI($title, $description);
            
            // Clean any output buffer
            ob_end_clean();
            
            if ($summary && !empty(trim($summary))) {
                echo json_encode([
                    'success' => true,
                    'summary' => trim($summary)
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => 'Erreur lors de la génération du résumé: résumé vide'
                ]);
            }
        } catch (Exception $e) {
            ob_end_clean();
            error_log("Error in generateAISummary: " . $e->getMessage() . " | Trace: " . $e->getTraceAsString());
            echo json_encode([
                'success' => false,
                'error' => 'Erreur serveur: ' . $e->getMessage()
            ]);
        } catch (Error $e) {
            ob_end_clean();
            error_log("Fatal error in generateAISummary: " . $e->getMessage() . " | Trace: " . $e->getTraceAsString());
            echo json_encode([
                'success' => false,
                'error' => 'Erreur fatale: ' . $e->getMessage()
            ]);
        }
        
        exit;
    }
    
    private function generateSummaryWithAI($title, $description) {
        try {
            // Validate inputs first
            if (empty($title) || empty($description)) {
                return $this->generateSimpleSummary($title, $description);
            }
            
            // Use OpenAI API or alternative
            // For now, we'll use a simple approach with OpenAI-compatible API
            // You can replace this with your preferred AI service
            
            $apiKey = getenv('OPENAI_API_KEY') ?? '';
            
            if (empty($apiKey)) {
                // Fallback: Generate a simple summary based on the description
                return $this->generateSimpleSummary($title, $description);
            }
            
            // Use OpenAI API
            $prompt = "Résume ce projet en un paragraphe simple et concis (3-4 phrases maximum) :\n\nTitre: $title\n\nDescription: $description\n\nRésumé:";
            
            $ch = curl_init('https://api.openai.com/v1/chat/completions');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Tu es un assistant qui résume des projets en français de manière concise et claire.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'max_tokens' => 150,
                'temperature' => 0.7
            ]));
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200) {
                $data = json_decode($response, true);
                if (isset($data['choices'][0]['message']['content'])) {
                    return trim($data['choices'][0]['message']['content']);
                }
            }
            
            // Fallback if API fails
            return $this->generateSimpleSummary($title, $description);
            
        } catch (Exception $e) {
            error_log("AI Summary generation error: " . $e->getMessage());
            // Fallback to simple summary
            return $this->generateSimpleSummary($title, $description);
        }
    }
    
    private function generateSimpleSummary($title, $description) {
        // Clean and prepare the description
        $description = trim($description);
        $description = preg_replace('/\s+/', ' ', $description); // Remove multiple spaces
        
        // Try to extract meaningful sentences
        $sentences = preg_split('/(?<=[.!?])\s+/', $description, -1, PREG_SPLIT_NO_EMPTY);
        
        // Filter out very short sentences and clean them
        $validSentences = [];
        foreach ($sentences as $sentence) {
            $sentence = trim($sentence);
            // Keep sentences that are meaningful (at least 20 characters and contain some content)
            if (strlen($sentence) >= 20 && preg_match('/[a-zA-ZÀ-ÿ]{3,}/', $sentence)) {
                $validSentences[] = $sentence;
            }
        }
        
        // If we have valid sentences, take the first 2-3 most important ones
        if (!empty($validSentences)) {
            $summarySentences = [];
            $totalLength = 0;
            $maxLength = 250; // Maximum length for summary
            
            foreach ($validSentences as $sentence) {
                if ($totalLength + strlen($sentence) <= $maxLength && count($summarySentences) < 3) {
                    $summarySentences[] = $sentence;
                    $totalLength += strlen($sentence);
                } else {
                    break;
                }
            }
            
            if (!empty($summarySentences)) {
                $summary = implode(' ', $summarySentences);
                // Ensure it ends with proper punctuation
                if (!preg_match('/[.!?]$/', $summary)) {
                    $summary = rtrim($summary, '.,;:') . '.';
                }
                return $summary;
            }
        }
        
        // Fallback: Create a smart summary from words
        $words = preg_split('/\s+/', $description);
        $words = array_filter($words, function($word) {
            // Remove very short words and special characters only
            return strlen(trim($word, '.,;:!?()[]{}"\'-_')) >= 3;
        });
        
        // Take first meaningful words (about 30-35 words for a good summary)
        $summaryWords = array_slice($words, 0, 35);
        $summary = implode(' ', $summaryWords);
        
        // Clean up and ensure proper ending
        $summary = trim($summary);
        if (strlen($summary) > 200) {
            // Cut at last complete word before 200 characters
            $summary = substr($summary, 0, 200);
            $lastSpace = strrpos($summary, ' ');
            if ($lastSpace !== false) {
                $summary = substr($summary, 0, $lastSpace);
            }
        }
        
        // Add ellipsis if we cut the text
        if (count($words) > 35) {
            $summary .= '...';
        } else {
            // Ensure proper punctuation if we have the full text
            if (!preg_match('/[.!?]$/', $summary)) {
                $summary = rtrim($summary, '.,;:') . '.';
            }
        }
        
        return $summary;
    }
}
?>

