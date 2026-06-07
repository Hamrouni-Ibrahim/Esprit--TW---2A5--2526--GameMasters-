<?php
require_once __DIR__ . '/../models/Reclamation.php';

class ReclamationController {
    private $reclamation;
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
        $this->reclamation = new Reclamation($db);
    }

    // User: Create reclamation
    public function create() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?action=login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->reclamation->titre = $_POST['titre'] ?? '';
            $this->reclamation->description = $_POST['description'] ?? '';
            $this->reclamation->user_id = $_SESSION['user_id'];
            $this->reclamation->image_path = null; // Can be extended for image upload

            if ($this->reclamation->create()) {
                $_SESSION['success_message'] = "Réclamation créée avec succès!";
                header("Location: ?action=mes_reclamations");
                exit;
            } else {
                $_SESSION['error_message'] = "Erreur lors de la création de la réclamation.";
            }
        }
        
        $pageTitle = 'Nouvelle Réclamation';
        $currentPage = 'reclamation_create';
        include "views/front/includes/header.php";
        include "views/front/reclamation_form.php";
        include "views/front/includes/footer.php";
    }

    // User: List user's reclamations
    public function mesReclamations() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?action=login");
            exit;
        }

        $stmt = $this->reclamation->getByUser($_SESSION['user_id']);
        
        // Prepare reclamations with edit info
        $reclamations = [];
        $tempStmt = $this->reclamation->getByUser($_SESSION['user_id']);
        while ($row = $tempStmt->fetch(PDO::FETCH_ASSOC)) {
            $row['can_edit'] = $this->reclamation->canEdit($row['id']);
            $row['time_remaining'] = $this->reclamation->getTimeRemaining($row['id']);
            $reclamations[] = $row;
        }
        
        $pageTitle = 'Mes Réclamations';
        $currentPage = 'mes_reclamations';
        include "views/front/includes/header.php";
        include "views/front/mes_reclamations.php";
        include "views/front/includes/footer.php";
    }

    // User: Edit reclamation
    public function edit() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?action=login");
            exit;
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if (!$this->reclamation->getById($id)) {
            $_SESSION['error_message'] = "Réclamation introuvable.";
            header("Location: ?action=mes_reclamations");
            exit;
        }

        if (!$this->reclamation->canEdit($id)) {
            $_SESSION['error_message'] = "Le délai de modification a expiré (30 minutes).";
            header("Location: ?action=mes_reclamations");
            exit;
        }

        // Vérifier que la réclamation appartient à l'utilisateur
        if ($this->reclamation->user_id != $_SESSION['user_id']) {
            $_SESSION['error_message'] = "Vous ne pouvez modifier que vos propres réclamations.";
            header("Location: ?action=mes_reclamations");
            exit;
        }

        $pageTitle = 'Modifier Réclamation';
        $currentPage = 'reclamation_edit';
        include "views/front/includes/header.php";
        include "views/front/edit_reclamation.php";
        include "views/front/includes/footer.php";
    }

    // User: Update reclamation
    public function update() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?action=login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

            if (!$this->reclamation->canEdit($id)) {
                $_SESSION['error_message'] = "Le délai de modification a expiré.";
                header("Location: ?action=mes_reclamations");
                exit;
            }

            $this->reclamation->id = $id;
            $this->reclamation->titre = $_POST['titre'] ?? '';
            $this->reclamation->description = $_POST['description'] ?? '';
            $this->reclamation->image_path = null; // Can be extended

            // Vérifier que la réclamation appartient à l'utilisateur
            if (!$this->reclamation->getById($id) || $this->reclamation->user_id != $_SESSION['user_id']) {
                $_SESSION['error_message'] = "Vous ne pouvez modifier que vos propres réclamations.";
                header("Location: ?action=mes_reclamations");
                exit;
            }

            if ($this->reclamation->update()) {
                $_SESSION['success_message'] = "Réclamation modifiée avec succès!";
                header("Location: ?action=mes_reclamations");
                exit;
            } else {
                $_SESSION['error_message'] = "Erreur lors de la modification.";
            }
        }

        header("Location: ?action=mes_reclamations");
        exit;
    }

    // User: Delete reclamation
    public function delete() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?action=login");
            exit;
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if (!$this->reclamation->getById($id)) {
            $_SESSION['error_message'] = "Réclamation introuvable.";
            header("Location: ?action=mes_reclamations");
            exit;
        }

        // Vérifier que la réclamation appartient à l'utilisateur et peut être supprimée
        if ($this->reclamation->user_id != $_SESSION['user_id']) {
            $_SESSION['error_message'] = "Vous ne pouvez supprimer que vos propres réclamations.";
            header("Location: ?action=mes_reclamations");
            exit;
        }

        if (!$this->reclamation->canEdit($id)) {
            $_SESSION['error_message'] = "Le délai de suppression a expiré (30 minutes).";
            header("Location: ?action=mes_reclamations");
            exit;
        }

        if ($this->reclamation->delete($id)) {
            $_SESSION['success_message'] = "Réclamation supprimée avec succès!";
        } else {
            $_SESSION['error_message'] = "Erreur lors de la suppression.";
        }

        header("Location: ?action=mes_reclamations");
        exit;
    }

    // Admin: List all reclamations
    public function adminList() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header("Location: ?action=login");
            exit;
        }

        $filters = [];
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $filters['search'] = $_GET['search'];
        }
        if (isset($_GET['statut']) && !empty($_GET['statut'])) {
            $filters['statut'] = $_GET['statut'];
        }
        if (isset($_GET['date_debut']) && !empty($_GET['date_debut'])) {
            $filters['date_debut'] = $_GET['date_debut'];
        }
        if (isset($_GET['date_fin']) && !empty($_GET['date_fin'])) {
            $filters['date_fin'] = $_GET['date_fin'];
        }
        if (isset($_GET['order_by']) && !empty($_GET['order_by'])) {
            $filters['order_by'] = $_GET['order_by'];
        }

        try {
            // Check if table exists first
            $tableCheck = $this->db->query("SHOW TABLES LIKE 'reclamations'");
            if ($tableCheck->rowCount() == 0) {
                error_log("Reclamations table does not exist!");
                $reclamations = [];
            } else {
                $stmt = !empty($filters) ? $this->reclamation->search($filters) : $this->reclamation->getAll();
                
                // Fetch all results into an array
                $reclamations = [];
                if ($stmt) {
                    $reclamations = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    // Log for debugging
                    error_log("Admin reclamations fetched: " . count($reclamations) . " records");
                }
            }
        } catch (Exception $e) {
            error_log("Error fetching reclamations in adminList: " . $e->getMessage());
            $reclamations = [];
        }

        $pageTitle = 'Gestion des Réclamations';
        $currentPage = 'admin_reclamations';
        include "views/admin/includes/header.php";
        include "views/admin/reclamations_list.php";
        include "views/admin/includes/footer.php";
    }

    // Admin: Respond to reclamation
    public function adminRespond() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header("Location: ?action=login");
            exit;
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->reclamation->id = $id;
            $this->reclamation->reponse = $_POST['reponse'] ?? '';

            if ($this->reclamation->addReponse()) {
                $_SESSION['admin_success'] = "Réponse envoyée avec succès!";
                header("Location: ?action=admin_reclamations");
                exit;
            } else {
                $_SESSION['admin_error'] = "Erreur lors de l'envoi de la réponse.";
            }
        }

        if (!$this->reclamation->getById($id)) {
            $_SESSION['admin_error'] = "Réclamation introuvable.";
            header("Location: ?action=admin_reclamations");
            exit;
        }

        $pageTitle = 'Répondre à la Réclamation';
        $currentPage = 'admin_reclamations';
        include "views/admin/includes/header.php";
        include "views/admin/reclamation_respond.php";
        include "views/admin/includes/footer.php";
    }

    // Admin: Edit reclamation
    public function adminEdit() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header("Location: ?action=login");
            exit;
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->reclamation->id = $id;
            $this->reclamation->titre = $_POST['titre'] ?? '';
            $this->reclamation->description = $_POST['description'] ?? '';
            $this->reclamation->reponse = $_POST['reponse'] ?? '';
            $this->reclamation->statut = $_POST['statut'] ?? 'en_attente';

            if ($this->reclamation->updateAdmin()) {
                $_SESSION['admin_success'] = "Réclamation modifiée avec succès!";
                header("Location: ?action=admin_reclamations");
                exit;
            } else {
                $_SESSION['admin_error'] = "Erreur lors de la modification.";
            }
        }

        if (!$this->reclamation->getById($id)) {
            $_SESSION['admin_error'] = "Réclamation introuvable.";
            header("Location: ?action=admin_reclamations");
            exit;
        }

        $pageTitle = 'Modifier la Réclamation';
        $currentPage = 'admin_reclamations';
        include "views/admin/includes/header.php";
        include "views/admin/reclamation_edit.php";
        include "views/admin/includes/footer.php";
    }

    // Admin: Delete reclamation
    public function adminDelete() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header("Location: ?action=login");
            exit;
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($this->reclamation->delete($id)) {
            $_SESSION['admin_success'] = "Réclamation supprimée avec succès!";
        } else {
            $_SESSION['admin_error'] = "Erreur lors de la suppression.";
        }

        header("Location: ?action=admin_reclamations");
        exit;
    }
}
?>

