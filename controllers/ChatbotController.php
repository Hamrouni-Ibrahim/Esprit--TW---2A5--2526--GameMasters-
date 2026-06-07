<?php
require_once "models/AIService.php";

class ChatbotController {
    
    private $aiService;

    public function __construct() {
        $this->aiService = new AIService();
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
            $response = $this->aiService->getResponse($userMessage);
            
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
}
?>
