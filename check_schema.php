<?php
require_once "config/database.php";

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "Checking users table structure...\n";
    
    // Check reset_code column
    $query = "SHOW COLUMNS FROM users LIKE 'reset_code'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    if($stmt->rowCount() == 0) {
        echo "Adding reset_code column...\n";
        $alter = "ALTER TABLE users ADD COLUMN reset_code VARCHAR(255) DEFAULT NULL";
        $db->exec($alter);
        echo "reset_code column added.\n";
    } else {
        echo "reset_code column exists.\n";
    }
    
    // Check reset_code_expires column
    $query = "SHOW COLUMNS FROM users LIKE 'reset_code_expires'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    if($stmt->rowCount() == 0) {
        echo "Adding reset_code_expires column...\n";
        $alter = "ALTER TABLE users ADD COLUMN reset_code_expires DATETIME DEFAULT NULL";
        $db->exec($alter);
        echo "reset_code_expires column added.\n";
    } else {
        echo "reset_code_expires column exists.\n";
    }
    
    echo "Database check complete.\n";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
