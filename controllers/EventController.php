<?php
require_once __DIR__ . '/../models/Event.php';

class EventController {
    private $db;
    private $model;

    public function __construct($db) {
        $this->db = $db;
        $this->model = new Event($db);
    }

    public function index() {
        try {
            // Get search term if provided
            $search = $_GET['search'] ?? '';
            
            // Get events (filtered by search if provided)
            if (!empty($search)) {
                $events = $this->model->searchEvents($search);
            } else {
                $events = $this->model->getAllEvents();
            }
            
            // Ensure events is an array
            if (!is_array($events)) {
                $events = [];
            }
            
            // Get user participations if logged in
            $userParticipations = [];
            if (isset($_SESSION['email'])) {
                try {
                    $userParticipations = $this->model->getUserParticipations($_SESSION['email']);
                    if (!is_array($userParticipations)) {
                        $userParticipations = [];
                    }
                } catch (Exception $e) {
                    error_log("Error getting user participations: " . $e->getMessage());
                    $userParticipations = [];
                }
            }
            
            $pageTitle = 'Événements - Game Master';
            $currentPage = 'events';
            require __DIR__ . '/../views/front/includes/header.php';
            require __DIR__ . '/../views/front/events/index.php';
            require __DIR__ . '/../views/front/includes/footer.php';
        } catch (Exception $e) {
            error_log("Error in EventController::index(): " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            // Fallback: show error page or redirect
            $pageTitle = 'Erreur - Game Master';
            $currentPage = 'events';
            require __DIR__ . '/../views/front/includes/header.php';
            echo '<div class="content-container" style="padding: 40px; text-align: center;">';
            echo '<h1 style="color: #ff6b6b;">Erreur</h1>';
            echo '<p style="color: #a0a0a0;">Une erreur est survenue lors du chargement des événements.</p>';
            echo '<p style="color: #666; font-size: 0.9em;">' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '</div>';
            require __DIR__ . '/../views/front/includes/footer.php';
        }
    }

    public function participate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $event_id = $_POST['event_id'] ?? null;
            
            // Get user info from session if logged in, otherwise from form
            if (isset($_SESSION['user_id']) && isset($_SESSION['username']) && isset($_SESSION['email'])) {
                $name = $_SESSION['username'];
                $email = $_SESSION['email'];
            } else {
                $name = $_POST['name'] ?? '';
                $email = $_POST['email'] ?? '';
            }
            
            if ($event_id && !empty($name) && !empty($email)) {
                // Check if already participated
                if ($this->model->checkUserParticipation($event_id, $email)) {
                    $_SESSION['event_error'] = "Vous avez déjà participé à cet événement.";
                } else {
                    $participation_id = $this->model->addParticipation($event_id, $name, $email);
                    if ($participation_id) {
                        $_SESSION['event_success'] = "Votre participation a été enregistrée avec succès !";
                        $_SESSION['participation_id'] = $participation_id; // Store for ticket download
                    } else {
                        $_SESSION['event_error'] = "Erreur lors de l'enregistrement de votre participation.";
                    }
                }
            } else {
                $_SESSION['event_error'] = "Veuillez remplir tous les champs requis.";
            }
            
            header('Location: ?action=events');
            exit();
        }
    }

    public function myParticipations() {
        // Check if user is logged in
        if (!isset($_SESSION['email'])) {
            $_SESSION['event_error'] = "Vous devez être connecté pour voir vos participations.";
            header('Location: ?action=login');
            exit();
        }
        
        $userParticipations = $this->model->getUserParticipations($_SESSION['email']);
        
        $pageTitle = 'Mes Participations - Game Master';
        $currentPage = 'participations';
        require __DIR__ . '/../views/front/includes/header.php';
        require __DIR__ . '/../views/front/events/participations.php';
        require __DIR__ . '/../views/front/includes/footer.php';
    }

    public function cancelParticipation() {
        // Check if user is logged in
        if (!isset($_SESSION['email'])) {
            $_SESSION['event_error'] = "Vous devez être connecté pour annuler une participation.";
            header('Location: ?action=login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $participation_id = $_POST['participation_id'] ?? null;
            
            if ($participation_id) {
                $result = $this->model->cancelUserParticipation($participation_id, $_SESSION['email']);
                if ($result) {
                    $_SESSION['event_success'] = "Votre participation a été annulée avec succès.";
                } else {
                    $_SESSION['event_error'] = "Erreur lors de l'annulation de votre participation. La participation n'existe pas ou ne vous appartient pas.";
                }
            } else {
                $_SESSION['event_error'] = "ID de participation invalide.";
            }
        }
        
        header('Location: ?action=my_participations');
        exit();
    }

    // Admin methods
    public function adminList() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: ?action=login");
            exit;
        }

        $events = $this->model->getAllEvents();
        
        $pageTitle = 'Gestion des Événements - Game Master';
        $currentPage = 'admin_events';
        require __DIR__ . '/../views/admin/includes/header.php';
        require __DIR__ . '/../views/admin/events.php';
        require __DIR__ . '/../views/admin/includes/footer.php';
    }

    public function adminAdd() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: ?action=login");
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'] ?? '';
            $date_debut = $_POST['date_debut'] ?? '';
            $heure_debut = $_POST['heure_debut'] ?? '00:00';
            $date_fin = $_POST['date_fin'] ?? '';
            $heure_fin = $_POST['heure_fin'] ?? '00:00';
            $description = $_POST['description'] ?? '';
            
            // Handle image upload
            $image = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'public/uploads/events/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $fileExtension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array(strtolower($fileExtension), $allowedExtensions)) {
                    $fileName = uniqid('event_') . '.' . $fileExtension;
                    $filePath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
                        $image = $filePath;
                    }
                }
            }
            
            // Combine date and time
            $datetime_debut = $date_debut . ' ' . $heure_debut . ':00';
            $datetime_fin = $date_fin . ' ' . $heure_fin . ':00';
            
            if (!empty($nom) && !empty($date_debut) && !empty($date_fin)) {
                // Validate dates
                $start = new DateTime($datetime_debut);
                $end = new DateTime($datetime_fin);
                
                if ($end <= $start) {
                    $_SESSION['admin_error'] = "La date de fin doit être après la date de début.";
                    header("Location: ?action=admin_events");
                    exit();
                }
                
                $result = $this->model->addEvent($nom, $datetime_debut, $datetime_fin, $description, $image);
                if ($result) {
                    $_SESSION['admin_success'] = "Événement ajouté avec succès !";
                } else {
                    $_SESSION['admin_error'] = "Erreur lors de l'ajout de l'événement.";
                }
            } else {
                $_SESSION['admin_error'] = "Veuillez remplir tous les champs requis.";
            }
            
            header("Location: ?action=admin_events");
            exit();
        }
    }

    public function adminEdit() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: ?action=login");
            exit;
        }

        $eventId = $_GET['id'] ?? null;
        if (!$eventId) {
            header("Location: ?action=admin_events");
            exit();
        }

        $event = $this->model->getEventById($eventId);
        if (!$event) {
            $_SESSION['admin_error'] = "Événement non trouvé.";
            header("Location: ?action=admin_events");
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'] ?? '';
            $date_debut = $_POST['date_debut'] ?? '';
            $heure_debut = $_POST['heure_debut'] ?? '00:00';
            $date_fin = $_POST['date_fin'] ?? '';
            $heure_fin = $_POST['heure_fin'] ?? '00:00';
            $description = $_POST['description'] ?? '';
            
            // Handle image upload
            $image = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'public/uploads/events/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $fileExtension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array(strtolower($fileExtension), $allowedExtensions)) {
                    $fileName = uniqid('event_') . '.' . $fileExtension;
                    $filePath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
                        $image = $filePath;
                        // Delete old image if exists
                        if (!empty($event['image']) && file_exists($event['image'])) {
                            unlink($event['image']);
                        }
                    }
                }
            } else {
                // Keep existing image if no new image uploaded
                $image = null; // null means don't update the image field
            }
            
            // Combine date and time
            $datetime_debut = $date_debut . ' ' . $heure_debut . ':00';
            $datetime_fin = $date_fin . ' ' . $heure_fin . ':00';
            
            if (!empty($nom) && !empty($date_debut) && !empty($date_fin)) {
                // Validate dates
                $start = new DateTime($datetime_debut);
                $end = new DateTime($datetime_fin);
                
                if ($end <= $start) {
                    $_SESSION['admin_error'] = "La date de fin doit être après la date de début.";
                    header("Location: ?action=admin_event_edit&id=" . $eventId);
                    exit();
                }
                
                $result = $this->model->updateEvent($eventId, $nom, $datetime_debut, $datetime_fin, $description, $image);
                if ($result) {
                    $_SESSION['admin_success'] = "Événement mis à jour avec succès !";
                } else {
                    $_SESSION['admin_error'] = "Erreur lors de la mise à jour de l'événement.";
                }
            } else {
                $_SESSION['admin_error'] = "Veuillez remplir tous les champs requis.";
            }
            
            header("Location: ?action=admin_events");
            exit();
        }

        $pageTitle = 'Modifier Événement - Game Master';
        $currentPage = 'admin_events';
        require __DIR__ . '/../views/admin/includes/header.php';
        require __DIR__ . '/../views/admin/event_edit.php';
        require __DIR__ . '/../views/admin/includes/footer.php';
    }

    public function adminDelete() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: ?action=login");
            exit;
        }

        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $result = $this->model->deleteEvent($id);
            if ($result) {
                $_SESSION['admin_success'] = "Événement supprimé avec succès !";
            } else {
                $_SESSION['admin_error'] = "Erreur lors de la suppression de l'événement.";
            }
            header("Location: ?action=admin_events");
            exit();
        }
    }

    // Admin Participation Management
    public function adminParticipations() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: ?action=login");
            exit;
        }

        $participations = $this->model->getAllParticipants();
        $events = $this->model->getAllEvents();
        
        $pageTitle = 'Gestion des Participations - Game Master';
        $currentPage = 'admin_participations';
        require __DIR__ . '/../views/admin/includes/header.php';
        require __DIR__ . '/../views/admin/participations.php';
        require __DIR__ . '/../views/admin/includes/footer.php';
    }

    public function adminAddParticipation() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: ?action=login");
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $event_id = $_POST['event_id'] ?? null;
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            
            if ($event_id && !empty($name) && !empty($email)) {
                // Check if already participated
                if ($this->model->checkUserParticipation($event_id, $email)) {
                    $_SESSION['admin_error'] = "Cet utilisateur participe déjà à cet événement.";
                } else {
                    $result = $this->model->addParticipation($event_id, $name, $email);
                    if ($result) {
                        $_SESSION['admin_success'] = "Participation ajoutée avec succès !";
                    } else {
                        $_SESSION['admin_error'] = "Erreur lors de l'ajout de la participation.";
                    }
                }
            } else {
                $_SESSION['admin_error'] = "Veuillez remplir tous les champs requis.";
            }
            
            header("Location: ?action=admin_participations");
            exit();
        }
    }

    public function adminEditParticipation() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: ?action=login");
            exit;
        }

        $participationId = $_GET['id'] ?? null;
        if (!$participationId) {
            header("Location: ?action=admin_participations");
            exit();
        }

        $participation = $this->model->getParticipationById($participationId);
        if (!$participation) {
            $_SESSION['admin_error'] = "Participation non trouvée.";
            header("Location: ?action=admin_participations");
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $event_id = $_POST['event_id'] ?? null;
            
            if (!empty($name) && !empty($email) && $event_id) {
                $result = $this->model->updateParticipation($participationId, $event_id, $name, $email);
                if ($result) {
                    $_SESSION['admin_success'] = "Participation mise à jour avec succès !";
                } else {
                    $_SESSION['admin_error'] = "Erreur lors de la mise à jour de la participation.";
                }
            } else {
                $_SESSION['admin_error'] = "Veuillez remplir tous les champs requis.";
            }
            
            header("Location: ?action=admin_participations");
            exit();
        }

        $events = $this->model->getAllEvents();
        
        $pageTitle = 'Modifier Participation - Game Master';
        $currentPage = 'admin_participations';
        require __DIR__ . '/../views/admin/includes/header.php';
        require __DIR__ . '/../views/admin/participation_edit.php';
        require __DIR__ . '/../views/admin/includes/footer.php';
    }

    public function adminDeleteParticipation() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: ?action=login");
            exit;
        }

        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $result = $this->model->deleteParticipant($id);
            if ($result) {
                $_SESSION['admin_success'] = "Participation supprimée avec succès !";
            } else {
                $_SESSION['admin_error'] = "Erreur lors de la suppression de la participation.";
            }
            header("Location: ?action=admin_participations");
            exit();
        }
    }
}
