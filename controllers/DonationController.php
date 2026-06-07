<?php
require_once __DIR__ . '/../models/Donation.php';

class DonationController {
    private $model;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->model = new Donation($db);
    }

    public function index() {
        // Front-end donation page with form and search
        // If user is logged in, use their email for search
        $searchEmail = isset($_GET['search_email']) ? $_GET['search_email'] : (isset($_SESSION['email']) ? $_SESSION['email'] : '');
        $selectedProjectId = isset($_GET['project_id']) ? (int)$_GET['project_id'] : null;
        $userDonations = [];
        
        // Auto-load user donations if logged in and no search email provided
        if (isset($_SESSION['email']) && empty($searchEmail)) {
            $searchEmail = $_SESSION['email'];
        }
        
        if (!empty($searchEmail)) {
            $userDonations = $this->model->getDonationsByEmail($searchEmail);
        }
        
        // Get all projects for dropdown (if projects table exists)
        $projects = [];
        $selectedProject = null;
        try {
            require_once __DIR__ . '/../models/Project.php';
            $projectModel = new Project($this->db);
            $projects = $projectModel->getAllProjects();
            
            // Get selected project details if project_id is provided
            if ($selectedProjectId) {
                $selectedProject = $projectModel->getProjectById($selectedProjectId);
            }
        } catch (Exception $e) {
            // Projects table might not exist, that's okay
            $projects = [];
        }
        
        $pageTitle = 'Donations - Game Master';
        $currentPage = 'donation';
        require __DIR__ . '/../views/front/includes/header.php';
        require __DIR__ . '/../views/front/donation/index.php';
        require __DIR__ . '/../views/front/includes/footer.php';
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if user is logged in
            if (!isset($_SESSION['user_id']) || !isset($_SESSION['username']) || !isset($_SESSION['email'])) {
                $_SESSION['donation_error'] = "Vous devez être connecté pour faire un don.";
                header('Location: ?action=login');
                exit();
            }
            
            // Get user info from session
            $name = $_SESSION['username'] ?? '';
            $email = $_SESSION['email'] ?? '';
            $amount = $_POST['amount'] ?? 0;
            $project_id = !empty($_POST['project_id']) ? $_POST['project_id'] : null;
            
            if (!empty($name) && !empty($email) && $amount > 0) {
                $result = $this->model->addDonation($name, $email, $amount, $project_id);
                if ($result) {
                    $_SESSION['donation_success'] = "Merci pour votre don de " . number_format($amount, 2, ',', ' ') . "€ !";
                } else {
                    $_SESSION['donation_error'] = "Erreur lors de l'ajout de votre don. Veuillez réessayer.";
                }
            } else {
                $_SESSION['donation_error'] = "Veuillez remplir tous les champs requis.";
            }
            
            // Redirect back to donation page
            $redirectUrl = '?action=donation';
            if ($project_id) {
                $redirectUrl .= '&project_id=' . $project_id;
            }
            header('Location: ' . $redirectUrl);
            exit();
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if user is logged in
            if (!isset($_SESSION['user_id']) || !isset($_SESSION['username']) || !isset($_SESSION['email'])) {
                $_SESSION['donation_error'] = "Vous devez être connecté pour modifier une donation.";
                header('Location: ?action=login');
                exit();
            }
            
            $id = $_POST['id'] ?? null;
            // Get user info from session if not provided
            $name = $_POST['name'] ?? $_SESSION['username'] ?? '';
            $email = $_POST['email'] ?? $_SESSION['email'] ?? '';
            $amount = $_POST['amount'] ?? 0;
            $project_id = !empty($_POST['project_id']) ? $_POST['project_id'] : null;
            $searchEmail = $_POST['search_email_redirect'] ?? '';
            
            if ($id && !empty($name) && !empty($email) && $amount > 0) {
                $result = $this->model->updateDonation($id, $name, $email, $amount, $project_id);
                if ($result) {
                    $_SESSION['donation_success'] = "Donation mise à jour avec succès !";
                } else {
                    $_SESSION['donation_error'] = "Erreur lors de la mise à jour.";
                }
            } else {
                $_SESSION['donation_error'] = "Veuillez remplir tous les champs requis.";
            }
            
            $redirectUrl = '?action=donation';
            if (!empty($searchEmail)) {
                $redirectUrl .= '&search_email=' . urlencode($searchEmail);
            }
            header('Location: ' . $redirectUrl);
            exit();
        }
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $result = $this->model->deleteDonation($_GET['id']);
            if ($result) {
                $_SESSION['donation_success'] = "Donation supprimée avec succès !";
            } else {
                $_SESSION['donation_error'] = "Erreur lors de la suppression.";
            }
            
            $redirectUrl = '?action=donation';
            $searchEmail = $_GET['search_email_redirect'] ?? '';
            if (!empty($searchEmail)) {
                $redirectUrl .= '&search_email=' . urlencode($searchEmail);
            }
            header('Location: ' . $redirectUrl);
            exit();
        }
    }

    // Admin methods
    public function adminList() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: ?action=login");
            exit;
        }

        $donations = $this->model->getAllDonations();
        $donationStats = $this->model->getStatistics();
        
        // Get all projects for dropdown (if projects table exists)
        $projects = [];
        try {
            require_once __DIR__ . '/../models/Project.php';
            $projectModel = new Project($this->db);
            $projects = $projectModel->getAllProjects();
        } catch (Exception $e) {
            // Projects table might not exist, that's okay
            $projects = [];
        }

        $pageTitle = 'Gestion des Donations - Game Master';
        $currentPage = 'admin_donations';
        require __DIR__ . '/../views/admin/includes/header.php';
        require __DIR__ . '/../views/admin/donations.php';
        require __DIR__ . '/../views/admin/includes/footer.php';
    }

    public function adminDelete() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: ?action=login");
            exit;
        }

        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $result = $this->model->deleteDonation($id);
            if ($result) {
                $_SESSION['admin_success'] = "Donation supprimée avec succès !";
            } else {
                $_SESSION['admin_error'] = "Erreur lors de la suppression de la donation.";
            }
            header('Location: ?action=admin_donations');
            exit();
        }
    }
}
?>

