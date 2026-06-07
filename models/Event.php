<?php
class Event {
    private $conn;
    private $table_name = "evenement";
    private $participation_table = "participation";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllEvents() {
        try {
            // First check if table exists
            $checkTable = "SHOW TABLES LIKE '" . $this->table_name . "'";
            $checkStmt = $this->conn->prepare($checkTable);
            $checkStmt->execute();
            if ($checkStmt->rowCount() === 0) {
                error_log("Table " . $this->table_name . " does not exist");
                return [];
            }
            
            // Try new structure first (date_debut, date_fin)
            try {
                // Check if date_debut column exists
                $checkCol = "SHOW COLUMNS FROM " . $this->table_name . " LIKE 'date_debut'";
                $checkColStmt = $this->conn->prepare($checkCol);
                $checkColStmt->execute();
                $hasNewStructure = $checkColStmt->rowCount() > 0;
                
                if ($hasNewStructure) {
                    $sql = "SELECT * FROM " . $this->table_name . " ORDER BY date_debut DESC";
                } else {
                    // Use old structure
                    $sql = "SELECT * FROM " . $this->table_name . " ORDER BY dateevent DESC";
                }
                
                $query = $this->conn->prepare($sql);
                $query->execute();
                $results = $query->fetchAll(PDO::FETCH_ASSOC);
                
                return is_array($results) ? $results : [];
            } catch (Exception $e) {
                error_log("Error fetching events (new structure): " . $e->getMessage());
                // Try old structure as fallback
                try {
                    $sql = "SELECT * FROM " . $this->table_name . " ORDER BY dateevent DESC";
                    $query = $this->conn->prepare($sql);
                    $query->execute();
                    $results = $query->fetchAll(PDO::FETCH_ASSOC);
                    return is_array($results) ? $results : [];
                } catch (Exception $e2) {
                    error_log("Error fetching events (old structure fallback): " . $e2->getMessage());
                    return [];
                }
            }
        } catch (Exception $e) {
            error_log("Error in getAllEvents(): " . $e->getMessage());
            return [];
        }
    }

    public function searchEvents($searchTerm) {
        if (empty($searchTerm)) {
            return $this->getAllEvents();
        }
        
        $searchTerm = "%" . $searchTerm . "%";
        
        // Try new structure first (date_debut, date_fin)
        try {
            $sql = "SELECT * FROM " . $this->table_name . " 
                    WHERE nom_evenet LIKE ? OR description LIKE ?
                    ORDER BY date_debut DESC";
            $query = $this->conn->prepare($sql);
            $query->execute([$searchTerm, $searchTerm]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Fallback to old structure (dateevent, duree)
            try {
                $sql = "SELECT * FROM " . $this->table_name . " 
                        WHERE nom_evenet LIKE ? OR description LIKE ?
                        ORDER BY dateevent DESC";
                $query = $this->conn->prepare($sql);
                $query->execute([$searchTerm, $searchTerm]);
                return $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e2) {
                error_log("Error searching events: " . $e2->getMessage());
                return [];
            }
        }
    }

    public function getEventById($id) {
        try {
            $sql = "SELECT * FROM " . $this->table_name . " WHERE idevent = ?";
            $query = $this->conn->prepare($sql);
            $query->execute([$id]);
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error fetching event: " . $e->getMessage());
            return null;
        }
    }

    public function addEvent($nom, $date_debut, $date_fin, $description, $image = null) {
        try {
            // Try new structure first (date_debut, date_fin)
            $sql = "INSERT INTO " . $this->table_name . " (nom_evenet, date_debut, date_fin, description, image) VALUES (?, ?, ?, ?, ?)";
            $query = $this->conn->prepare($sql);
            $result = $query->execute([$nom, $date_debut, $date_fin, $description, $image]);
            if (!$result) {
                error_log("Event insertion failed: " . print_r($query->errorInfo(), true));
                return false;
            }
            return $result;
        } catch (Exception $e) {
            // Fallback to old structure if new columns don't exist
            try {
                $sql = "INSERT INTO " . $this->table_name . " (nom_evenet, dateevent, duree, description) VALUES (?, ?, ?, ?)";
                $query = $this->conn->prepare($sql);
                $date = date('Y-m-d', strtotime($date_debut));
                $duree = $this->calculateDuration($date_debut, $date_fin);
                $result = $query->execute([$nom, $date, $duree, $description]);
                return $result;
            } catch (Exception $e2) {
                error_log("Event insertion error: " . $e2->getMessage());
                return false;
            }
        }
    }
    
    private function calculateDuration($date_debut, $date_fin) {
        $start = new DateTime($date_debut);
        $end = new DateTime($date_fin);
        $interval = $start->diff($end);
        $hours = ($interval->days * 24) + $interval->h;
        $minutes = $interval->i;
        return sprintf('%02d:%02d:00', $hours, $minutes);
    }

    public function updateEvent($id, $nom, $date_debut, $date_fin, $description, $image = null) {
        try {
            // Try new structure first (date_debut, date_fin)
            if ($image !== null) {
                $sql = "UPDATE " . $this->table_name . " SET nom_evenet = ?, date_debut = ?, date_fin = ?, description = ?, image = ? WHERE idevent = ?";
                $query = $this->conn->prepare($sql);
                $query->execute([$nom, $date_debut, $date_fin, $description, $image, $id]);
            } else {
                $sql = "UPDATE " . $this->table_name . " SET nom_evenet = ?, date_debut = ?, date_fin = ?, description = ? WHERE idevent = ?";
                $query = $this->conn->prepare($sql);
                $query->execute([$nom, $date_debut, $date_fin, $description, $id]);
            }
            return true;
        } catch (Exception $e) {
            // Fallback to old structure if new columns don't exist
            try {
                $sql = "UPDATE " . $this->table_name . " SET nom_evenet = ?, dateevent = ?, duree = ?, description = ? WHERE idevent = ?";
                $query = $this->conn->prepare($sql);
                $date = date('Y-m-d', strtotime($date_debut));
                $duree = $this->calculateDuration($date_debut, $date_fin);
                $query->execute([$nom, $date, $duree, $description, $id]);
                return true;
            } catch (Exception $e2) {
                error_log("Event update error: " . $e2->getMessage());
                return false;
            }
        }
    }

    public function deleteEvent($id) {
        try {
            $sql = "DELETE FROM " . $this->table_name . " WHERE idevent = ?";
            $query = $this->conn->prepare($sql);
            $query->execute([$id]);
            return true;
        } catch (Exception $e) {
            error_log("Event delete error: " . $e->getMessage());
            return false;
        }
    }

    public function getUserParticipations($email) {
        if (!$email) return [];
        try {
            // Try new structure first
            $sql = "SELECT p.*, e.nom_evenet, e.date_debut, e.date_fin, e.description, e.image
                    FROM " . $this->participation_table . " p 
                    INNER JOIN " . $this->table_name . " e ON p.idevent = e.idevent 
                    WHERE p.email = :email
                    ORDER BY p.date_participation DESC";
            $query = $this->conn->prepare($sql);
            $query->execute(['email' => $email]);
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            
            $keyed_results = [];
            foreach ($results as $row) {
                $keyed_results[$row['idevent']] = $row;
            }
            return $keyed_results;
        } catch(Exception $e) {
            // Fallback to old structure
            try {
                $sql = "SELECT p.*, e.nom_evenet, e.dateevent, e.duree, e.description, e.image
                        FROM " . $this->participation_table . " p 
                        INNER JOIN " . $this->table_name . " e ON p.idevent = e.idevent 
                        WHERE p.email = :email
                        ORDER BY p.date_participation DESC";
                $query = $this->conn->prepare($sql);
                $query->execute(['email' => $email]);
                $results = $query->fetchAll(PDO::FETCH_ASSOC);
                
                $keyed_results = [];
                foreach ($results as $row) {
                    $keyed_results[$row['idevent']] = $row;
                }
                return $keyed_results;
            } catch(Exception $e2) {
                error_log("Error fetching user participations: " . $e2->getMessage());
                return [];
            }
        }
    }

    public function addParticipation($event_id, $name, $email) {
        try {
            // Check if user already participated
            $checkSql = "SELECT id FROM " . $this->participation_table . " WHERE idevent = ? AND email = ?";
            $checkQuery = $this->conn->prepare($checkSql);
            $checkQuery->execute([$event_id, $email]);
            if ($checkQuery->rowCount() > 0) {
                return false; // Already participated
            }
            
            $sql = "INSERT INTO " . $this->participation_table . " (idevent, nom, email, date_participation) VALUES (?, ?, ?, NOW())";
            $query = $this->conn->prepare($sql);
            $query->execute([$event_id, $name, $email]);
            return $this->conn->lastInsertId(); // Return the participation ID
        } catch (Exception $e) {
            error_log("Participation error: " . $e->getMessage());
            return false;
        }
    }

    public function getAllParticipants() {
        try {
            // Try to get all event fields (new structure)
            try {
                $sql = "SELECT p.*, e.nom_evenet, e.date_debut, e.date_fin, e.description, e.image 
                        FROM " . $this->participation_table . " p 
                        LEFT JOIN " . $this->table_name . " e ON p.idevent = e.idevent 
                        ORDER BY p.date_participation DESC";
                $query = $this->conn->prepare($sql);
                $query->execute();
                return $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                // Fallback to old structure
                error_log("Error fetching participants with new structure, trying old: " . $e->getMessage());
                $sql = "SELECT p.*, e.nom_evenet 
                        FROM " . $this->participation_table . " p 
                        LEFT JOIN " . $this->table_name . " e ON p.idevent = e.idevent 
                        ORDER BY p.date_participation DESC";
                $query = $this->conn->prepare($sql);
                $query->execute();
                return $query->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            error_log("Error fetching participants: " . $e->getMessage());
            return [];
        }
    }

    public function deleteParticipant($id) {
        try {
            $sql = "DELETE FROM " . $this->participation_table . " WHERE id = ?";
            $query = $this->conn->prepare($sql);
            $query->execute([$id]);
            return true;
        } catch (Exception $e) {
            error_log("Error deleting participant: " . $e->getMessage());
            return false;
        }
    }

    public function cancelUserParticipation($participation_id, $user_email) {
        try {
            // Verify that the participation belongs to the user
            $checkSql = "SELECT id FROM " . $this->participation_table . " WHERE id = ? AND email = ?";
            $checkQuery = $this->conn->prepare($checkSql);
            $checkQuery->execute([$participation_id, $user_email]);
            
            if ($checkQuery->rowCount() === 0) {
                return false; // Participation doesn't belong to user or doesn't exist
            }
            
            // Delete the participation
            $sql = "DELETE FROM " . $this->participation_table . " WHERE id = ? AND email = ?";
            $query = $this->conn->prepare($sql);
            $query->execute([$participation_id, $user_email]);
            return true;
        } catch (Exception $e) {
            error_log("Error canceling participation: " . $e->getMessage());
            return false;
        }
    }

    public function getParticipationById($id) {
        try {
            // Try new structure first (date_debut, date_fin)
            try {
                $sql = "SELECT p.*, e.nom_evenet, e.date_debut, e.date_fin, e.description 
                        FROM " . $this->participation_table . " p 
                        LEFT JOIN " . $this->table_name . " e ON p.idevent = e.idevent 
                        WHERE p.id = ?";
                $query = $this->conn->prepare($sql);
                $query->execute([$id]);
                return $query->fetch(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                // Fallback to old structure
                $sql = "SELECT p.*, e.nom_evenet, e.dateevent, e.duree, e.description 
                        FROM " . $this->participation_table . " p 
                        LEFT JOIN " . $this->table_name . " e ON p.idevent = e.idevent 
                        WHERE p.id = ?";
                $query = $this->conn->prepare($sql);
                $query->execute([$id]);
                return $query->fetch(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            error_log("Error fetching participation: " . $e->getMessage());
            return null;
        }
    }

    public function updateParticipation($id, $event_id, $name, $email) {
        try {
            // Check if email already exists for another participation in the same event
            $checkSql = "SELECT id FROM " . $this->participation_table . " WHERE idevent = ? AND email = ? AND id != ?";
            $checkQuery = $this->conn->prepare($checkSql);
            $checkQuery->execute([$event_id, $email, $id]);
            if ($checkQuery->rowCount() > 0) {
                return false; // Email already exists for another participation
            }
            
            $sql = "UPDATE " . $this->participation_table . " SET idevent = ?, nom = ?, email = ? WHERE id = ?";
            $query = $this->conn->prepare($sql);
            $query->execute([$event_id, $name, $email, $id]);
            return true;
        } catch (Exception $e) {
            error_log("Error updating participation: " . $e->getMessage());
            return false;
        }
    }

    public function checkUserParticipation($event_id, $email) {
        try {
            // Try with 'id' first, then check for 'idparticipation' if needed
            $sql = "SELECT id FROM " . $this->participation_table . " WHERE idevent = ? AND email = ?";
            $query = $this->conn->prepare($sql);
            $query->execute([$event_id, $email]);
            if ($query->rowCount() > 0) {
                return true;
            }
            // Try alternative column name if exists
            $sql = "SELECT idparticipation FROM " . $this->participation_table . " WHERE idevent = ? AND email = ?";
            $query = $this->conn->prepare($sql);
            $query->execute([$event_id, $email]);
            return $query->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Error checking participation: " . $e->getMessage());
            return false;
        }
    }
}
