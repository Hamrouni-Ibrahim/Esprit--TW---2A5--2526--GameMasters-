<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | ' : ''; ?>Admin Game Masters</title>
    
    <!-- Template Back CSS - Using main admin header template -->
    <!-- Note: This layout is replaced by views/admin/includes/header.php -->
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <?php
    // Récupérer les infos de l'admin connecté
    $adminName = $_SESSION['username'] ?? 'Admin';
    $currentUserId = $_SESSION['user_id'] ?? 0;
    
    // Fetch admin's actual avatar from database
    $adminAvatar = '';
    $adminAvatarFallback = strtoupper(substr($adminName, 0, 1));
    $adminAvatarColor = '';
    
    if($currentUserId > 0) {
        try {
            require_once __DIR__ . '/../../config/database.php';
            require_once __DIR__ . '/../../models/User.php';
            $database = new Database();
            $db = $database->getConnection();
            
            $query = "SELECT avatar, username FROM users WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$currentUserId]);
            $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($userRow) {
                $adminAvatar = $userRow['avatar'] ?? '';
                $adminAvatarFallback = strtoupper(substr($userRow['username'], 0, 1));
                
                // Generate color from username
                $hash = md5($userRow['username']);
                $adminAvatarColor = '#' . substr($hash, 0, 6);
            }
        } catch(Exception $e) {
            error_log("Error fetching admin avatar: " . $e->getMessage());
        }
    }

    if(isset($profileController) && isset($_SESSION['user_id'])) {
        $adminProfile = $profileController->getByUserId($_SESSION['user_id']);
        if($adminProfile && !empty($adminProfile['first_name']) && !empty($adminProfile['last_name'])) {
            $adminName = $adminProfile['first_name'] . ' ' . $adminProfile['last_name'];
        }
    }
    ?>

    <style>
        /* Styles spécifiques admin */
        :root {
            --sidebar-width: 260px;
            --navbar-height: 70px;
        }

        .admin-wrapper {
            display: flex;
            min-height: 100vh;
            padding-top: var(--navbar-height);
        }

        /* Top Navbar */
        .top-navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: var(--navbar-height);
            background: rgba(18, 18, 18, 0.98);
            backdrop-filter: blur(30px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            z-index: 1002;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 30px;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .navbar-logo {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 15px;
            border: 2px solid var(--accent-purple);
        }

        .navbar-title {
            font-size: 20px;
            font-weight: 900;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .navbar-title .prism {
            background: linear-gradient(135deg, var(--text-primary), var(--accent-cyan));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .navbar-title .flux {
            color: var(--text-secondary);
            font-weight: 300;
            margin-left: 5px;
        }

        .navbar-profile {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: nowrap;
        }
        
        .navbar-logout-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 8px 15px;
            background: transparent;
            border: 1px solid var(--accent-red);
            color: var(--accent-red);
            border-radius: 8px;
            text-decoration: none;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            white-space: nowrap;
            margin-left: 10px;
        }
        
        .navbar-logout-btn:hover {
            background: var(--accent-red);
            color: var(--text-primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 68, 68, 0.3);
        }

        .profile-info {
            text-align: right;
            display: none;
        }

        @media (min-width: 768px) {
            .profile-info {
                display: block;
            }
        }
        
        @media (max-width: 768px) {
            .navbar-logout-btn .logout-text {
                display: none;
            }
            .navbar-logout-btn {
                padding: 8px 10px;
                min-width: 40px;
            }
        }

        .profile-name {
            color: var(--text-primary);
            font-weight: 700;
            font-size: 14px;
        }

        .profile-role {
            color: var(--accent-cyan);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .profile-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            border: 2px solid var(--accent-purple);
            object-fit: cover;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .profile-avatar:hover {
            border-color: var(--accent-cyan);
            transform: scale(1.1);
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.3);
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: rgba(18, 18, 18, 0.98);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            position: fixed;
            top: var(--navbar-height);
            left: 0;
            height: calc(100vh - var(--navbar-height));
            z-index: 1000;
            display: flex;
            flex-direction: column;
            backdrop-filter: blur(30px);
            padding-top: 20px;
        }

        .sidebar-menu {
            padding: 10px 0;
            flex-grow: 1;
            overflow-y: auto;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 15px 30px;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-size: 14px;
            border-left: 3px solid transparent;
        }

        .sidebar-link:hover, .sidebar-link.active {
            background: rgba(255, 255, 255, 0.03);
            color: var(--text-primary);
            border-left-color: var(--accent-purple);
        }

        .sidebar-link i, .sidebar-link span.icon {
            margin-right: 15px;
            width: 20px;
            text-align: center;
        }

        .sidebar-footer {
            padding: 20px 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* Admin Content Adjustment */
        .admin-content {
            flex-grow: 1;
            margin-left: var(--sidebar-width);
            background: var(--primary-black);
            color: var(--text-primary);
            min-height: calc(100vh - var(--navbar-height));
            display: flex;
            flex-direction: column;
        }
        
        .admin-main {
            padding: 40px;
            max-width: 1600px;
            margin: 0 auto;
            width: 100%;
            flex-grow: 1;
        }
        
        .admin-header {
            margin-bottom: 50px;
        }
        
        .admin-title {
            font-size: 42px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 3px;
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 15px;
            line-height: 1.2;
        }
        
        .admin-subtitle {
            color: var(--text-secondary);
            font-size: 16px;
            max-width: 800px;
            line-height: 1.6;
        }
        
        /* Cartes admin */
        .admin-card {
            background: linear-gradient(135deg, rgba(42, 42, 42, 0.3), rgba(26, 26, 26, 0.5));
            border: 1px solid var(--metal-dark);
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .admin-card:hover {
            border-color: var(--accent-purple);
            box-shadow: 0 15px 35px rgba(153, 69, 255, 0.1);
        }
        
        .admin-card h3 {
            color: var(--accent-cyan);
            margin-bottom: 25px;
            font-size: 24px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        
        .action-card {
            background: linear-gradient(135deg, var(--carbon-medium), var(--carbon-dark));
            border: 1px solid var(--metal-dark);
            border-radius: 15px;
            padding: 30px 25px;
            text-align: center;
            cursor: pointer;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }
        
        .action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, var(--accent-purple), var(--accent-blue));
            transition: left 0.4s ease;
        }
        
        .action-card:hover::before {
            left: 100%;
        }
        
        .action-card:hover {
            transform: translateY(-8px);
            border-color: var(--accent-purple);
            box-shadow: 0 15px 35px rgba(153, 69, 255, 0.2);
        }
        
        .action-card .skill-icon-hex {
            font-size: 48px;
            margin-bottom: 20px;
            display: block;
            animation: float 3s ease-in-out infinite;
        }
        
        .action-card h3 {
            color: var(--text-primary);
            margin: 0 0 15px 0;
            font-size: 20px;
            font-weight: 700;
        }
        
        .action-card p {
            color: var(--text-secondary);
            font-size: 14px;
            line-height: 1.5;
            margin: 0;
        }
        
        /* Tables admin */
        .admin-table {
            width: 100%;
            background: var(--carbon-dark);
            border-radius: 15px;
            overflow: hidden;
            border: 1px solid var(--metal-dark);
        }
        
        .admin-table th {
            background: linear-gradient(135deg, var(--carbon-medium), var(--carbon-dark));
            color: var(--accent-cyan);
            padding: 20px;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 14px;
            border-bottom: 1px solid var(--metal-dark);
        }
        
        .admin-table td {
            padding: 18px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: var(--text-secondary);
        }
        
        .admin-table tr:hover td {
            background: rgba(153, 69, 255, 0.05);
            color: var(--text-primary);
        }
        
        /* Badges */
        .status-badge {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .status-active {
            background: rgba(0, 255, 136, 0.1);
            color: var(--accent-green);
            border: 1px solid var(--accent-green);
        }
        
        .status-inactive {
            background: rgba(255, 68, 68, 0.1);
            color: var(--accent-red);
            border: 1px solid var(--accent-red);
        }
        
        .status-pending {
            background: rgba(255, 170, 0, 0.1);
            color: #ffaa00;
            border: 1px solid #ffaa00;
        }
        
        .status-banned {
            background: rgba(255, 68, 68, 0.1);
            color: var(--accent-red);
            border: 1px solid var(--accent-red);
        }
        
        /* Boutons admin */
        .btn-admin {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 12px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-edit {
            background: transparent;
            border: 1px solid var(--accent-blue);
            color: var(--accent-blue);
        }
        
        .btn-edit:hover {
            background: var(--accent-blue);
            color: var(--text-primary);
        }
        
        .btn-delete {
            background: transparent;
            border: 1px solid var(--accent-red);
            color: var(--accent-red);
        }
        
        .btn-delete:hover {
            background: var(--accent-red);
            color: var(--text-primary);
        }
        
        .btn-add {
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue));
            color: var(--text-primary);
            border: none;
            padding: 12px 25px;
        }
        
        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(153, 69, 255, 0.3);
        }
        
        /* Forms admin */
        .admin-form {
            background: linear-gradient(135deg, rgba(42, 42, 42, 0.3), rgba(26, 26, 26, 0.5));
            border: 1px solid var(--metal-dark);
            border-radius: 15px;
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            color: var(--accent-cyan);
            margin-bottom: 10px;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
            font-weight: 600;
        }
        
        .form-control {
            width: 100%;
            padding: 15px;
            background: var(--carbon-dark);
            border: 1px solid var(--metal-dark);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--accent-purple);
            box-shadow: 0 0 15px rgba(153, 69, 255, 0.2);
        }
        
        /* Alertes */
        .alert {
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            border: 1px solid transparent;
        }
        
        .alert-success {
            background: rgba(0, 255, 136, 0.1);
            color: var(--accent-green);
            border-color: rgba(0, 255, 136, 0.3);
        }
        
        .alert-error {
            background: rgba(255, 68, 68, 0.1);
            color: var(--accent-red);
            border-color: rgba(255, 68, 68, 0.3);
        }
        
        /* Images utilisateurs */
        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--accent-purple);
        }
        
        .user-avatar-small {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--accent-blue);
        }
        
        .user-avatar-large {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--accent-cyan);
            margin: 0 auto 20px;
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, rgba(42, 42, 42, 0.5), rgba(26, 26, 26, 0.8));
            border: 1px solid var(--metal-dark);
            border-radius: 15px;
            padding: 30px 25px;
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            border-color: var(--accent-purple);
            box-shadow: 0 15px 35px rgba(153, 69, 255, 0.2);
        }
        
        .stat-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 30px;
            color: var(--text-primary);
            box-shadow: 0 10px 25px rgba(153, 69, 255, 0.3);
        }
        
        .stat-number {
            font-size: 42px;
            font-weight: 900;
            color: var(--text-primary);
            margin-bottom: 10px;
            font-family: 'Orbitron', monospace;
        }
        
        .stat-label {
            font-size: 14px;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 15px;
        }
        
        .stat-description {
            font-size: 13px;
            color: var(--text-dim);
            line-height: 1.5;
        }

        /* Footer Admin */
        .admin-footer {
            margin-top: auto;
            padding: 30px 40px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            text-align: center;
            color: var(--text-dim);
            font-size: 12px;
        }

        /* Responsive */
        @media (max-width: 992px) {
            :root {
                --sidebar-width: 0px;
            }
            
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
                transition: transform 0.3s ease;
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .admin-content {
                margin-left: 0;
            }
            
            .mobile-toggle {
                display: block;
                position: fixed;
                top: 15px;
                left: 15px;
                z-index: 1003;
                background: var(--accent-purple);
                color: white;
                border: none;
                padding: 10px;
                border-radius: 5px;
                cursor: pointer;
            }
        }
        
        @media (min-width: 993px) {
            .mobile-toggle {
                display: none;
            }
        }

        /* Correction du loader */
        .loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: var(--primary-black);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10000;
            transition: opacity 0.5s, visibility 0.5s;
        }

        .loader.hidden {
            opacity: 0;
            visibility: hidden;
        }

        .loader-content {
            text-align: center;
        }

        .loader-prism {
            width: 100px;
            height: 100px;
            position: relative;
            margin: 0 auto 30px;
        }

        .prism-face {
            position: absolute;
            width: 100%;
            height: 100%;
            border: 2px solid transparent;
            transform-origin: center;
            animation: prismRotate 3s linear infinite;
        }

        .prism-face:nth-child(1) {
            border-image: linear-gradient(45deg, var(--accent-red), var(--accent-blue)) 1;
            animation-delay: 0s;
        }

        .prism-face:nth-child(2) {
            border-image: linear-gradient(45deg, var(--accent-blue), var(--accent-green)) 1;
            transform: rotate(60deg);
            animation-delay: 0.2s;
        }

        .prism-face:nth-child(3) {
            border-image: linear-gradient(45deg, var(--accent-green), var(--accent-purple)) 1;
            transform: rotate(120deg);
            animation-delay: 0.4s;
        }

        @keyframes prismRotate {
            0% {
                transform: rotate(0deg) scale(1);
            }
            50% {
                transform: rotate(180deg) scale(1.2);
            }
            100% {
                transform: rotate(360deg) scale(1);
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        /* Modal styles */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: var(--card-bg);
            margin: 20px;
            padding: 30px;
            border-radius: 15px;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            border: 1px solid rgba(255,255,255,0.1);
            position: relative;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .modal-header h2 {
            color: var(--accent-cyan);
            margin: 0;
        }

        .close-btn {
            background: transparent;
            border: none;
            color: var(--text-color);
            font-size: 1.5em;
            cursor: pointer;
            padding: 5px;
        }

        .close-btn:hover {
            color: var(--accent-red);
        }
        
        /* Modal Profil Admin */
        .profile-modal-content {
            background: linear-gradient(135deg, rgba(26, 26, 26, 0.98), rgba(18, 18, 18, 0.98));
            border: 1px solid var(--accent-purple);
            box-shadow: 0 20px 60px rgba(153, 69, 255, 0.3);
            max-width: 700px;
            max-height: 85vh;
        }
        
        .profile-modal-header {
            text-align: center;
            padding: 30px;
            background: linear-gradient(135deg, rgba(153, 69, 255, 0.1), rgba(0, 255, 255, 0.1));
            border-radius: 15px 15px 0 0;
            margin: -30px -30px 30px -30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .profile-modal-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 4px solid var(--accent-purple);
            object-fit: cover;
            margin: 0 auto 20px;
            display: block;
            box-shadow: 0 10px 30px rgba(153, 69, 255, 0.4);
        }
        
        .profile-modal-name {
            font-size: 28px;
            font-weight: 900;
            color: var(--text-primary);
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .profile-modal-username {
            font-size: 16px;
            color: var(--text-secondary);
            margin-bottom: 15px;
            font-weight: 400;
        }
        
        .profile-modal-role {
            font-size: 14px;
            color: var(--accent-cyan);
            text-transform: uppercase;
            letter-spacing: 2px;
            padding: 8px 20px;
            background: rgba(0, 255, 255, 0.1);
            border: 1px solid var(--accent-cyan);
            border-radius: 20px;
            display: inline-block;
        }
        
        /* Sections du profil */
        .profile-section {
            margin-bottom: 25px;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .profile-section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px 20px;
            background: linear-gradient(135deg, rgba(153, 69, 255, 0.15), rgba(0, 255, 255, 0.05));
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 16px;
            font-weight: 700;
            color: var(--accent-cyan);
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        
        .section-icon {
            font-size: 20px;
        }
        
        .profile-section-content {
            padding: 20px;
        }
        
        .profile-modal-info {
            padding: 20px 0;
        }
        
        .profile-info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .profile-info-item:last-child {
            border-bottom: none;
        }
        
        .profile-info-label {
            color: var(--text-secondary);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }
        
        .profile-info-value {
            color: var(--text-primary);
            font-size: 15px;
            font-weight: 500;
            text-align: right;
        }
        
        .profile-bio {
            color: var(--text-secondary);
            font-size: 14px;
            line-height: 1.8;
            margin: 0;
            padding: 10px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            border-left: 3px solid var(--accent-purple);
        }
        
        .profile-modal-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .profile-modal-actions .btn-admin {
            flex: 1;
        }
    </style>
</head>
<body>
    <!-- Loading Screen -->
    <div class="loader" id="loader">
        <div class="loader-content">
            <div class="loader-prism">
                <div class="prism-face"></div>
                <div class="prism-face"></div>
                <div class="prism-face"></div>
            </div>
            <div style="color: var(--accent-purple); font-size: 18px; text-transform: uppercase; letter-spacing: 3px;">Chargement Admin...</div>
        </div>
    </div>

    <!-- Top Navbar -->
    <header class="top-navbar">
        <a href="?action=admin_dashboard" class="navbar-brand">
            <img src="public/assets/img/logo.png" alt="Game Masters Logo" class="navbar-logo" onerror="this.style.display='none'">
            <div class="navbar-title">
                <span class="prism">ADMIN</span>
                <span class="flux">SYSTEM</span>
            </div>
        </a>
        
        <div class="navbar-profile">
            <div class="profile-info">
                <div class="profile-name"><?php echo htmlspecialchars($adminName); ?></div>
                <div class="profile-role">Administrateur</div>
            </div>
            <?php if(!empty($adminAvatar) && file_exists($_SERVER['DOCUMENT_ROOT'] . $adminAvatar)): ?>
                <img src="<?php echo htmlspecialchars($adminAvatar); ?>" alt="Admin" class="profile-avatar" onclick="showAdminProfile()" title="Voir mon profil">
            <?php else: ?>
                <div class="profile-avatar" onclick="showAdminProfile()" title="Voir mon profil" style="background-color: <?php echo $adminAvatarColor; ?>; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 18px; color: #fff;">
                    <?php echo $adminAvatarFallback; ?>
                </div>
            <?php endif; ?>
            <a href="?action=logout&redirect=home" 
               class="navbar-logout-btn" 
               onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?')"
               title="Déconnexion">
                <span>🚪</span>
                <span class="logout-text">Déconnexion</span>
            </a>
        </div>
    </header>

    <!-- Mobile Toggle -->
    <button class="mobile-toggle" id="sidebarToggle">
        ☰
    </button>

    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <nav class="sidebar-menu">
                <a href="?action=admin_dashboard" class="sidebar-link">
                    <span class="icon">📊</span> Dashboard
                </a>
                <a href="?action=admin_games" class="sidebar-link">
                    <span class="icon">🎮</span> Jeux
                </a>
                <a href="?action=admin_users" class="sidebar-link">
                    <span class="icon">👥</span> Utilisateurs
                </a>
                <a href="?action=admin_category_search" class="sidebar-link">
                    <span class="icon">🔍</span> Recherche
                </a>
                <div style="margin: 20px 0; border-top: 1px solid rgba(255,255,255,0.05);"></div>
                <a href="?action=profile" class="sidebar-link">
                    <span class="icon">👤</span> Mon Profil
                </a>
                <a href="index.php" class="sidebar-link">
                    <span class="icon">🌐</span> Site Public
                </a>
            </nav>
            
            <div class="sidebar-footer">
                <a href="?action=logout&redirect=home" 
                   class="sidebar-link" 
                   style="color: var(--accent-red);"
                   onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?')">
                    <span class="icon">🚪</span> Déconnexion
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="admin-content">
            <div class="admin-main">
                <?php echo $content; ?>
            </div>
            
            <footer class="admin-footer">
                <div class="copyright">
                    © 2024 Game Masters. Panel Admin - Template PRISM FLUX
                </div>
            </footer>
        </div>
    </div>

    <!-- Modal Profil Admin -->
    <div class="modal" id="adminProfileModal">
        <div class="modal-content profile-modal-content">
            <button class="close-btn" onclick="hideModal('adminProfileModal')" style="position: absolute; top: 20px; right: 20px; z-index: 10;">×</button>
            
            <div class="profile-modal-header">
                <?php if(!empty($adminAvatar) && file_exists($_SERVER['DOCUMENT_ROOT'] . $adminAvatar)): ?>
                    <img src="<?php echo htmlspecialchars($adminAvatar); ?>" alt="Admin" class="profile-modal-avatar">
                <?php else: ?>
                    <div class="profile-modal-avatar" style="background-color: <?php echo $adminAvatarColor; ?>; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 48px; color: #fff;">
                        <?php echo $adminAvatarFallback; ?>
                    </div>
                <?php endif; ?>
                <div class="profile-modal-name"><?php echo htmlspecialchars($adminName); ?></div>
                <div class="profile-modal-username">@<?php echo htmlspecialchars($_SESSION['username'] ?? 'admin'); ?></div>
                <div class="profile-modal-role">Administrateur</div>
            </div>
            
            <!-- Section Informations du Compte -->
            <div class="profile-section">
                <div class="profile-section-title">
                    <span class="section-icon">📋</span>
                    Informations du Compte
                </div>
                <div class="profile-section-content">
                    <div class="profile-info-item">
                        <span class="profile-info-label">Rôle</span>
                        <span class="profile-info-value">Administrateur</span>
                    </div>
                    <div class="profile-info-item">
                        <span class="profile-info-label">Statut</span>
                        <span class="profile-info-value"><span class="status-badge status-active">Actif</span></span>
                    </div>
                    <div class="profile-info-item">
                        <span class="profile-info-label">ID Utilisateur</span>
                        <span class="profile-info-value">#<?php echo htmlspecialchars($_SESSION['user_id'] ?? '0'); ?></span>
                    </div>
                    <?php if(isset($adminProfile) && !empty($adminProfile['created_at'])): ?>
                    <div class="profile-info-item">
                        <span class="profile-info-label">Date d'inscription</span>
                        <span class="profile-info-value"><?php echo date('d/m/Y', strtotime($adminProfile['created_at'])); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="profile-info-item">
                        <span class="profile-info-label">Fuseau horaire</span>
                        <span class="profile-info-value">Europe/Paris</span>
                    </div>
                </div>
            </div>
            
            <!-- Section Informations Personnelles -->
            <?php if(isset($adminProfile)): ?>
            <div class="profile-section">
                <div class="profile-section-title">
                    <span class="section-icon">👤</span>
                    Informations Personnelles
                </div>
                <div class="profile-section-content">
                    <?php if(!empty($adminProfile['country'])): ?>
                    <div class="profile-info-item">
                        <span class="profile-info-label">Pays</span>
                        <span class="profile-info-value"><?php echo htmlspecialchars($adminProfile['country']); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if(!empty($adminProfile['nationality'])): ?>
                    <div class="profile-info-item">
                        <span class="profile-info-label">Nationalité</span>
                        <span class="profile-info-value"><?php echo htmlspecialchars($adminProfile['nationality']); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if(!empty($adminProfile['gender'])): ?>
                    <div class="profile-info-item">
                        <span class="profile-info-label">Genre</span>
                        <span class="profile-info-value"><?php echo htmlspecialchars($adminProfile['gender']); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if(!empty($adminProfile['birth_date'])): ?>
                    <div class="profile-info-item">
                        <span class="profile-info-label">Date de naissance</span>
                        <span class="profile-info-value"><?php echo date('d/m/Y', strtotime($adminProfile['birth_date'])); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if(!empty($adminProfile['city'])): ?>
                    <div class="profile-info-item">
                        <span class="profile-info-label">Ville</span>
                        <span class="profile-info-value"><?php echo htmlspecialchars($adminProfile['city']); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if(!empty($adminProfile['address'])): ?>
                    <div class="profile-info-item">
                        <span class="profile-info-label">Adresse</span>
                        <span class="profile-info-value"><?php echo htmlspecialchars($adminProfile['address']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Section Informations Professionnelles -->
            <?php if(!empty($adminProfile['career_level']) || !empty($adminProfile['expertise']) || !empty($adminProfile['tech_stack'])): ?>
            <div class="profile-section">
                <div class="profile-section-title">
                    <span class="section-icon">💼</span>
                    Informations Professionnelles
                </div>
                <div class="profile-section-content">
                    <?php if(!empty($adminProfile['career_level'])): ?>
                    <div class="profile-info-item">
                        <span class="profile-info-label">Niveau de carrière</span>
                        <span class="profile-info-value"><?php echo htmlspecialchars($adminProfile['career_level']); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if(!empty($adminProfile['expertise'])): ?>
                    <div class="profile-info-item">
                        <span class="profile-info-label">Expertise</span>
                        <span class="profile-info-value"><?php echo htmlspecialchars($adminProfile['expertise']); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if(!empty($adminProfile['tech_stack'])): ?>
                    <div class="profile-info-item">
                        <span class="profile-info-label">Stack technique</span>
                        <span class="profile-info-value"><?php echo htmlspecialchars($adminProfile['tech_stack']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Section Contact -->
            <div class="profile-section">
                <div class="profile-section-title">
                    <span class="section-icon">📞</span>
                    Contact
                </div>
                <div class="profile-section-content">
                    <?php if(!empty($adminProfile['discord'])): ?>
                    <div class="profile-info-item">
                        <span class="profile-info-label">Discord</span>
                        <span class="profile-info-value"><?php echo htmlspecialchars($adminProfile['discord']); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if(!empty($adminProfile['email'])): ?>
                    <div class="profile-info-item">
                        <span class="profile-info-label">Email</span>
                        <span class="profile-info-value"><?php echo htmlspecialchars($adminProfile['email']); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if(!empty($adminProfile['phone'])): ?>
                    <div class="profile-info-item">
                        <span class="profile-info-label">Téléphone</span>
                        <span class="profile-info-value"><?php echo htmlspecialchars($adminProfile['phone']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Section Bio -->
            <?php if(!empty($adminProfile['bio'])): ?>
            <div class="profile-section">
                <div class="profile-section-title">
                    <span class="section-icon">📝</span>
                    Bio
                </div>
                <div class="profile-section-content">
                    <p class="profile-bio"><?php echo nl2br(htmlspecialchars($adminProfile['bio'])); ?></p>
                </div>
            </div>
            <?php endif; ?>
            <?php endif; ?>
            
            <div class="profile-modal-actions">
                <a href="?action=profile" class="btn-admin btn-edit">✏️ Modifier le Profil</a>
                <a href="?action=logout&redirect=home" class="btn-admin btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?')">🚪 Déconnexion</a>
                <button onclick="hideModal('adminProfileModal')" class="btn-admin" style="background: transparent; border: 1px solid var(--text-secondary); color: var(--text-secondary);">❌ Fermer</button>
            </div>
        </div>
    </div>

    <!-- Template Back JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded - Hiding loader');
            
            // Cacher le loader
            const loader = document.getElementById('loader');
            if (loader) {
                setTimeout(() => {
                    loader.classList.add('hidden');
                }, 500);
            }

            // Sidebar toggle mobile
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                });
                
                // Fermer la sidebar en cliquant en dehors sur mobile
                document.addEventListener('click', function(e) {
                    if (window.innerWidth <= 992 && 
                        !sidebar.contains(e.target) && 
                        !sidebarToggle.contains(e.target) && 
                        sidebar.classList.contains('active')) {
                        sidebar.classList.remove('active');
                    }
                });
            }

            // Lien actif
            const currentUrl = window.location.href;
            const sidebarLinks = document.querySelectorAll('.sidebar-link');
            sidebarLinks.forEach(link => {
                if (currentUrl.includes(link.getAttribute('href'))) {
                    link.classList.add('active');
                }
            });

            // Animation des cartes
            setTimeout(() => {
                const cards = document.querySelectorAll('.admin-card, .action-card, .stat-card');
                cards.forEach((card, index) => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                });
            }, 300);

            // Animation des compteurs
            const counters = document.querySelectorAll('.stat-number[data-target]');
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-target'));
                if (isNaN(target)) return;
                
                const duration = 2000;
                const step = target / (duration / 16);
                let current = 0;
                
                const updateCounter = setInterval(() => {
                    current += step;
                    if (current >= target) {
                        counter.textContent = target;
                        clearInterval(updateCounter);
                    } else {
                        counter.textContent = Math.floor(current);
                    }
                }, 16);
            });

            // Fermer les modales avec Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const modals = document.querySelectorAll('.modal');
                    modals.forEach(modal => {
                        modal.style.display = 'none';
                    });
                }
            });
        });
        
        // Fonctions utilitaires
        function confirmDelete(message = 'Êtes-vous sûr de vouloir supprimer cet élément ?') {
            return confirm(message);
        }

        function showModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'flex';
            }
        }

        function hideModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'none';
            }
        }

        // Fermer les modales en cliquant à l'extérieur
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        });

        // Voir les détails d'un utilisateur
        function viewUserDetails(userId) {
            window.location.href = 'index.php?action=admin_user_details&id=' + userId;
        }

        // Fonction de déconnexion améliorée
        function logoutUser() {
            if (confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
                // Redirection immédiate
                window.location.href = '?action=logout&redirect=home';
                return true;
            }
            return false;
        }
        
        // Afficher le profil de l'admin
        function showAdminProfile() {
            showModal('adminProfileModal');
        }
    </script>
</body>
</html>