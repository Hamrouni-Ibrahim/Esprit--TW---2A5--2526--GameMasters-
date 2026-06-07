<?php
class Project {
    private $conn;
    private $table_name = "projects";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllProjects($sort = 'date_desc') {
        $orderBy = $this->getOrderByClause($sort);
        try {
            $stmt = $this->conn->query("SELECT * FROM projects ORDER BY $orderBy");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get all projects error: " . $e->getMessage());
            $orderBy = $this->getFallbackOrderByClause($sort);
            $stmt = $this->conn->query("SELECT * FROM projects ORDER BY $orderBy");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    public function searchProjects($query, $sort = 'date_desc') {
        $searchTerm = "%" . $query . "%";
        $orderBy = $this->getOrderByClause($sort);
        try {
            $stmt = $this->conn->prepare("SELECT * FROM projects WHERE title LIKE ? OR description LIKE ? ORDER BY $orderBy");
            $stmt->execute([$searchTerm, $searchTerm]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Search projects error: " . $e->getMessage());
            $orderBy = $this->getFallbackOrderByClause($sort);
            $stmt = $this->conn->prepare("SELECT * FROM projects WHERE title LIKE ? OR description LIKE ? ORDER BY $orderBy");
            $stmt->execute([$searchTerm, $searchTerm]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    private function getOrderByClause($sort) {
        switch ($sort) {
            case 'alpha_asc': return "title ASC";
            case 'alpha_desc': return "title DESC";
            case 'date_asc': return "created_at ASC, id ASC";
            default: return "created_at DESC, id DESC";
        }
    }

    private function getFallbackOrderByClause($sort) {
        switch ($sort) {
            case 'alpha_asc': return "title ASC";
            case 'alpha_desc': return "title DESC";
            case 'date_asc': return "id ASC";
            default: return "id DESC";
        }
    }

    public function getProjectById($id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM projects WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get project by ID error: " . $e->getMessage());
            return null;
        }
    }

    public function addProject($title, $category, $image, $description) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO projects (title, category, image, description) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $category, $image, $description]);
            return true;
        } catch (Exception $e) {
            error_log("Add project error: " . $e->getMessage());
            return false;
        }
    }

    public function updateProject($id, $title, $category, $image, $description) {
        try {
            $stmt = $this->conn->prepare("UPDATE projects SET title = ?, category = ?, image = ?, description = ? WHERE id = ?");
            $stmt->execute([$title, $category, $image, $description, $id]);
            return true;
        } catch (Exception $e) {
            error_log("Update project error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteProject($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM projects WHERE id = ?");
            $stmt->execute([$id]);
            return true;
        } catch (Exception $e) {
            error_log("Delete project error: " . $e->getMessage());
            return false;
        }
    }

    public function getStatistics() {
        $stats = [];
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) as count FROM projects");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['total_projects'] = $result['count'] ?? 0;
            
            $stmt = $this->conn->query("SELECT COUNT(DISTINCT category) as count FROM projects");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['total_categories'] = $result['count'] ?? 0;
            
            $stmt = $this->conn->query("SELECT category, COUNT(*) as count FROM projects GROUP BY category");
            $stats['projects_per_category'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get project statistics error: " . $e->getMessage());
            $stats['total_projects'] = 0;
            $stats['total_categories'] = 0;
            $stats['projects_per_category'] = [];
        }
        return $stats;
    }
}
?>




