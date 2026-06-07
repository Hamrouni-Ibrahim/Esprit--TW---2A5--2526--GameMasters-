
<?php
require_once "models/Category.php";
require_once "models/Formation.php";
require_once "models/Education.php";
require_once "config/database.php";

class CategoryController {

    public function search() {
        include "views/front/search_content.php";
    }

    // CHANGE FROM private TO public
    public function createCategoryIfNotExists($categoryName) {
        try {
            $pdo = (new Database())->getConnection();
            
            // Check if category already exists
            $checkQuery = $pdo->prepare("SELECT id FROM categories WHERE nom = ?");
            $checkQuery->execute([$categoryName]);
            $existing = $checkQuery->fetch(PDO::FETCH_ASSOC);
            
            if ($existing) {
                return $existing['id']; // Return existing category ID
            }
            
            // Create new category
            $insertQuery = $pdo->prepare("INSERT INTO categories (nom) VALUES (?)");
            $insertQuery->execute([$categoryName]);
            return $pdo->lastInsertId();
            
        } catch (PDOException $e) {
            // If table doesn't exist, create it
            if (strpos($e->getMessage(), 'categories') !== false) {
                $this->createCategoriesTable();
                return $this->createCategoryIfNotExists($categoryName); // Retry
            }
            return null;
        }
    }

    // Method to create categories table if needed
    private function createCategoriesTable() {
        $pdo = (new Database())->getConnection();
        $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
            id INT PRIMARY KEY AUTO_INCREMENT,
            nom VARCHAR(100) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
    }

    public function getAllCategories() {
        try {
            $pdo = (new Database())->getConnection();
            $query = $pdo->prepare("SELECT * FROM categories ORDER BY nom ASC");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // If table doesn't exist, create it and return empty array
            $this->createCategoriesTable();
            return [];
        }
    }

    // Rest of your existing methods...
    public function getFormationsByCategory($idCategory) {
        try {
            $pdo = (new Database())->getConnection();
            $query = $pdo->prepare("
                SELECT f.*, c.nom as category_name 
                FROM formations f 
                LEFT JOIN categories c ON f.category_id = c.id 
                WHERE f.category_id = :id
            ");
            $query->execute(['id' => $idCategory]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo $e->getMessage();
            return [];
        }
    }

    public function getEducationsByCategory($idCategory) {
        try {
            $pdo = (new Database())->getConnection();
            $query = $pdo->prepare("
                SELECT e.*, c.nom as category_name 
                FROM educations e 
                LEFT JOIN categories c ON e.category_id = c.id 
                WHERE e.category_id = :id
            ");
            $query->execute(['id' => $idCategory]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo $e->getMessage();
            return [];
        }
    }

    public function getCategoriesByType($type) {
        // For now, return all categories since we're not using types
        return $this->getAllCategories();
    }
    public function adminSearch() {
        include "views/admin/search_content.php";
    }

    public function adminList() {
        $categoryModel = new Category();
        $categories = $categoryModel->getAll();
        include "views/admin/categories_list.php";
    }

    public function adminAdd() {
        $pageTitle = 'Ajouter une Catégorie - Game Master';
        $currentPage = 'categories';

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['category'])) {
            $categoryModel = new Category();
            $data = ['nom' => trim($_POST['nom'])];
            $categoryModel->create($data);
            header("Location: ?controller=category&action=adminList");
            exit;
        }

        include "views/admin/categories_add.php";
    }

    public function adminEdit() {
        $pageTitle = 'Modifier une Catégorie - Game Master';
        $currentPage = 'categories';

        $categoryModel = new Category();
        $category = $categoryModel->getById($_GET['id']);

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['category'])) {
            $data = ['nom' => trim($_POST['nom'])];
            $categoryModel->update($_GET['id'], $data);
            header("Location: ?controller=category&action=adminList");
            exit;
        }

        include "views/admin/categories_edit.php";
    }

    public function adminDelete() {
        if (isset($_GET['id'])) {
            $categoryModel = new Category();
            $categoryModel->delete($_GET['id']);
        }
        header("Location: ?controller=category&action=adminList");
        exit;
    }
}