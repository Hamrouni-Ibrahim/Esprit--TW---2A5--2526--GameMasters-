<?php
class Profile {
    private $conn;
    private $table_name = "profiles";

    public $id;
    public $user_id;
    public $first_name;
    public $last_name;
    public $discord;
    public $country;
    public $nationality;
    public $gender;
    public $birth_date;
    public $career_level;
    public $expertise;
    public $tech_stack;
    public $timezone;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Créer ou mettre à jour le profil
    public function save() {
        // Vérifier si le profil existe déjà
        $check_query = "SELECT id FROM " . $this->table_name . " WHERE user_id = ?";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bindParam(1, $this->user_id);
        $check_stmt->execute();

        if($check_stmt->rowCount() > 0) {
            // Mettre à jour
            return $this->update();
        } else {
            // Créer
            return $this->create();
        }
    }

    // Créer un profil
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET user_id=:user_id, first_name=:first_name, last_name=:last_name, 
                      discord=:discord, country=:country, nationality=:nationality,
                      gender=:gender, birth_date=:birth_date, career_level=:career_level,
                      expertise=:expertise, tech_stack=:tech_stack, timezone=:timezone";
        
        $stmt = $this->conn->prepare($query);
        
        // Nettoyage des données
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->discord = htmlspecialchars(strip_tags($this->discord));
        
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":discord", $this->discord);
        $stmt->bindParam(":country", $this->country);
        $stmt->bindParam(":nationality", $this->nationality);
        $stmt->bindParam(":gender", $this->gender);
        $stmt->bindParam(":birth_date", $this->birth_date);
        $stmt->bindParam(":career_level", $this->career_level);
        $stmt->bindParam(":expertise", $this->expertise);
        $stmt->bindParam(":tech_stack", $this->tech_stack);
        $stmt->bindParam(":timezone", $this->timezone);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Mettre à jour le profil
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET first_name=:first_name, last_name=:last_name, discord=:discord, 
                      country=:country, nationality=:nationality, gender=:gender,
                      birth_date=:birth_date, career_level=:career_level, expertise=:expertise,
                      tech_stack=:tech_stack, timezone=:timezone
                  WHERE user_id=:user_id";
        
        $stmt = $this->conn->prepare($query);
        
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->discord = htmlspecialchars(strip_tags($this->discord));
        
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":discord", $this->discord);
        $stmt->bindParam(":country", $this->country);
        $stmt->bindParam(":nationality", $this->nationality);
        $stmt->bindParam(":gender", $this->gender);
        $stmt->bindParam(":birth_date", $this->birth_date);
        $stmt->bindParam(":career_level", $this->career_level);
        $stmt->bindParam(":expertise", $this->expertise);
        $stmt->bindParam(":tech_stack", $this->tech_stack);
        $stmt->bindParam(":timezone", $this->timezone);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Lire le profil par user_id - VERSION AMÉLIORÉE
    public function readByUserId() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_id);
        
        if(!$stmt->execute()) {
            return false;
        }
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->id = $row['id'];
            $this->first_name = $row['first_name'] ?? '';
            $this->last_name = $row['last_name'] ?? '';
            $this->discord = $row['discord'] ?? '';
            $this->country = $row['country'] ?? '';
            $this->nationality = $row['nationality'] ?? '';
            $this->gender = $row['gender'] ?? '';
            $this->birth_date = $row['birth_date'] ?? '';
            $this->career_level = $row['career_level'] ?? '';
            $this->expertise = $row['expertise'] ?? '';
            $this->tech_stack = $row['tech_stack'] ?? '';
            $this->timezone = $row['timezone'] ?? 'Europe/Paris';
            return true;
        }
        return false;
    }

    // NOUVELLE MÉTHODE : Vérifier si le profil existe
    public function exists() {
        $query = "SELECT id FROM " . $this->table_name . " WHERE user_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    // NOUVELLE MÉTHODE : Vérifier si le profil est complété
    public function isCompleted() {
        if(!$this->readByUserId()) {
            return false;
        }
        
        return !empty(trim($this->first_name)) && !empty(trim($this->last_name));
    }
}
?>