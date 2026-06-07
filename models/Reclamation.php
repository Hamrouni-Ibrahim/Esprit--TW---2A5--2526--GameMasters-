<?php
class Reclamation {
    private $conn;
    private $table_name = "reclamations";
    
    public $id;
    public $titre;
    public $description;
    public $image_path;
    public $user_id;
    public $reponse;
    public $statut;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                 SET titre=:titre, description=:description, image_path=:image_path, user_id=:user_id, statut='en_attente'";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":titre", $this->titre);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":image_path", $this->image_path);
        $stmt->bindParam(":user_id", $this->user_id);

        return $stmt->execute();
    }

    public function getAll() {
        try {
            $query = "SELECT r.*, u.username 
                      FROM " . $this->table_name . " r
                      LEFT JOIN users u ON r.user_id = u.id
                      ORDER BY r.created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            error_log("Error in Reclamation::getAll(): " . $e->getMessage());
            // Return empty result set on error
            $query = "SELECT 1 WHERE 1=0";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }
    }

    public function getByUser($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        return $stmt;
    }

    public function addReponse() {
        $query = "UPDATE " . $this->table_name . " 
                 SET reponse = :reponse, statut = 'traité', updated_at = NOW()
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":reponse", $this->reponse);
        $stmt->bindParam(":id", $this->id);
        
        return $stmt->execute();
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->id = $row['id'];
            $this->titre = $row['titre'];
            $this->description = $row['description'];
            $this->image_path = $row['image_path'];
            $this->reponse = $row['reponse'];
            $this->statut = $row['statut'];
            $this->user_id = $row['user_id'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'] ?? null;
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                 SET titre=:titre, description=:description, image_path=:image_path, updated_at = NOW()
                 WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":titre", $this->titre);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":image_path", $this->image_path);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    public function updateAdmin() {
        $query = "UPDATE " . $this->table_name . "
                 SET titre=:titre, description=:description, reponse=:reponse, statut=:statut, updated_at = NOW()
                 WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":titre", $this->titre);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":reponse", $this->reponse);
        $stmt->bindParam(":statut", $this->statut);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    public function canEdit($id) {
        $query = "SELECT created_at FROM " . $this->table_name . " WHERE id = ? AND statut = 'en_attente' LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $created_time = strtotime($row['created_at']);
            $current_time = time();
            $time_diff = $current_time - $created_time;
            
            return $time_diff <= 1800; // 30 minutes
        }

        return false;
    }

    public function getTimeRemaining($id) {
        $query = "SELECT created_at FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $created_time = strtotime($row['created_at']);
            $current_time = time();
            $time_diff = $current_time - $created_time;
            $remaining = 1800 - $time_diff; // 30 minutes en secondes

            return max(0, $remaining);
        }

        return 0;
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);

        return $stmt->execute();
    }

    public function search($filters = []) {
        $query = "SELECT r.*, u.username 
                  FROM " . $this->table_name . " r
                  LEFT JOIN users u ON r.user_id = u.id
                  WHERE 1=1";
        $params = [];

        // Filtre par statut
        if (!empty($filters['statut'])) {
            $query .= " AND r.statut = ?";
            $params[] = $filters['statut'];
        }

        // Filtre par recherche textuelle (titre ou description)
        if (!empty($filters['search'])) {
            $query .= " AND (r.titre LIKE ? OR r.description LIKE ?)";
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }

        // Filtre par date de début
        if (!empty($filters['date_debut'])) {
            $query .= " AND DATE(r.created_at) >= ?";
            $params[] = $filters['date_debut'];
        }

        // Filtre par date de fin
        if (!empty($filters['date_fin'])) {
            $query .= " AND DATE(r.created_at) <= ?";
            $params[] = $filters['date_fin'];
        }

        // Tri
        $order_by = "r.created_at DESC";
        if (!empty($filters['order_by'])) {
            switch ($filters['order_by']) {
                case 'date_asc':
                    $order_by = "r.created_at ASC";
                    break;
                case 'date_desc':
                    $order_by = "r.created_at DESC";
                    break;
                case 'titre_asc':
                    $order_by = "r.titre ASC";
                    break;
                case 'titre_desc':
                    $order_by = "r.titre DESC";
                    break;
                case 'statut_asc':
                    $order_by = "r.statut ASC";
                    break;
                case 'statut_desc':
                    $order_by = "r.statut DESC";
                    break;
            }
        }

        $query .= " ORDER BY " . $order_by;

        $stmt = $this->conn->prepare($query);

        // Liaison des paramètres
        for ($i = 0; $i < count($params); $i++) {
            $stmt->bindParam($i + 1, $params[$i]);
        }

        $stmt->execute();
        return $stmt;
    }
}
?>

