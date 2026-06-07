<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Admin - Game Master'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="public/css/templatemo-prism-flux.css">
    <style>
        /* CSS Variables for Prism Loader */
        :root {
            --primary-black: #0a0a0a;
            --accent-red: #ff3333;
            --accent-blue: #00a8ff;
            --accent-green: #00ff88;
            --accent-purple: #9945ff;
        }
        
        /* Admin Layout with Left Sidebar */
        :root {
            --sidebar-width: 280px;
            --header-height: 70px;
        }
        
        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            background: #0a0a0a;
            position: relative;
            min-height: 100vh;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(15, 15, 18, 0.15) 0%, rgba(18, 18, 22, 0.15) 25%, rgba(20, 20, 24, 0.15) 50%, rgba(15, 15, 18, 0.15) 75%, rgba(18, 18, 22, 0.15) 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            z-index: 0;
            pointer-events: none;
        }
        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
            background: rgba(10, 10, 12, 0.9);
            position: relative;
            z-index: 1;
            width: 100%;
        }
        
        /* Left Sidebar */
        .admin-sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, rgba(15, 15, 18, 0.98) 0%, rgba(18, 18, 22, 0.98) 50%, rgba(20, 20, 24, 0.98) 100%);
            border-right: 1px solid rgba(232, 121, 249, 0.3);
            box-shadow: 2px 0 20px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            overflow-x: hidden;
        }
        
        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(232, 121, 249, 0.2);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .sidebar-logo {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .sidebar-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 4px;
        }
        
        .sidebar-title {
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            background: linear-gradient(135deg, #ffffff, #e879f9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .sidebar-nav {
            flex: 1;
            padding: 20px 0;
        }
        
        .sidebar-nav-item {
            display: block;
            padding: 15px 25px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: all 0.3s ease;
            position: relative;
            border-left: 3px solid transparent;
        }
        
        .sidebar-nav-item:hover {
            background: rgba(232, 121, 249, 0.1);
            color: #e879f9;
            border-left-color: #e879f9;
        }
        
        .sidebar-nav-item.active {
            background: rgba(232, 121, 249, 0.15);
            color: #00a8ff;
            border-left-color: #00a8ff;
        }
        
        .sidebar-nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: #00a8ff;
            box-shadow: 0 0 10px rgba(0, 168, 255, 0.5);
        }
        
        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid rgba(232, 121, 249, 0.2);
        }
        
        .sidebar-logout-btn {
            width: 100%;
            padding: 12px 20px;
            background: transparent;
            border: 1px solid #ff3333;
            color: #ff3333;
            border-radius: 8px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: center;
            transition: all 0.3s ease;
            display: block;
        }
        
        .sidebar-logout-btn:hover {
            background: #ff3333;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 51, 51, 0.3);
        }
        
        /* Top Header Bar */
        .admin-top-header {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--header-height);
            background: linear-gradient(90deg, rgba(15, 15, 18, 0.97) 0%, rgba(18, 18, 22, 0.97) 50%, rgba(20, 20, 24, 0.97) 100%);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(232, 121, 249, 0.3);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 0 30px;
            z-index: 999;
        }
        
        .admin-profile-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .admin-profile-info {
            text-align: right;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        
        .admin-profile-name {
            font-size: 14px;
            font-weight: 600;
            color: #ffffff;
        }
        
        .admin-profile-role {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.6);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .admin-profile-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #9333ea, #c084fc);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
            border: 2px solid rgba(232, 121, 249, 0.3);
            transition: transform 0.3s ease;
        }
        
        .admin-profile-section:hover .admin-profile-avatar {
            transform: scale(1.1);
            box-shadow: 0 0 20px rgba(232, 121, 249, 0.5);
        }
        
        /* Styles pour le modal de profil admin */
        #adminProfileModal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            z-index: 10000;
            display: none;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        /* Main Content Area */
        .admin-content {
            position: relative;
            margin-left: var(--sidebar-width);
            margin-top: var(--header-height);
            padding: 40px 50px 80px;
            min-height: calc(100vh - var(--header-height));
            overflow-x: auto;
            overflow-y: visible;
            width: calc(100% - var(--sidebar-width));
            max-width: calc(100vw - var(--sidebar-width));
            background: linear-gradient(135deg, rgba(15, 15, 18, 0.15) 0%, rgba(18, 18, 22, 0.15) 50%, rgba(20, 20, 24, 0.15) 100%);
            box-sizing: border-box;
        }
        
        /* Admin Background Animations */
        .admin-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.6;
            background-image:
                radial-gradient(circle at 20% 50%, rgba(232, 121, 249, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(0, 168, 255, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 40% 20%, rgba(147, 51, 234, 0.12) 0%, transparent 50%),
                linear-gradient(45deg, transparent 30%, rgba(232, 121, 249, 0.08) 50%, transparent 70%),
                linear-gradient(-45deg, transparent 30%, rgba(0, 168, 255, 0.08) 50%, transparent 70%);
            animation: adminBgShift 20s ease-in-out infinite;
            pointer-events: none;
            z-index: 0;
        }
        
        .admin-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
            z-index: 0;
            display: none !important; /* Hidden - removed spider web shapes */
            visibility: hidden !important;
            opacity: 0 !important;
        }
        
        .admin-shape {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
        }
        
        .admin-shape {
            position: absolute;
            border: 2px solid rgba(232, 121, 249, 0.15);
        }
        
        .admin-shape.shape1 {
            width: 300px;
            height: 300px;
            top: 10%;
            left: -150px;
            transform: rotate(45deg);
            animation: adminFloat 15s ease-in-out infinite;
        }
        
        .admin-shape.shape2 {
            width: 200px;
            height: 200px;
            top: 60%;
            right: -100px;
            border-color: rgba(147, 51, 234, 0.15);
            animation: adminFloat 20s ease-in-out infinite reverse;
        }
        
        .admin-shape.shape3 {
            width: 150px;
            height: 150px;
            bottom: 20%;
            left: 10%;
            border-color: rgba(192, 132, 252, 0.15);
            transform: rotate(30deg);
            animation: adminFloat 18s ease-in-out infinite;
        }
        
        .admin-shape.shape4 {
            width: 250px;
            height: 250px;
            top: 15%;
            right: 5%;
            border-color: rgba(232, 121, 249, 0.12);
            transform: rotate(45deg);
            animation: adminFloat 16s ease-in-out infinite;
        }
        
        .admin-shape.shape5 {
            width: 180px;
            height: 180px;
            top: 55%;
            right: 12%;
            border-color: rgba(147, 51, 234, 0.12);
            transform: rotate(-30deg);
            animation: adminFloat 22s ease-in-out infinite reverse;
        }
        
        .admin-shape.shape6 {
            width: 120px;
            height: 120px;
            bottom: 15%;
            right: 8%;
            border-color: rgba(192, 132, 252, 0.12);
            transform: rotate(20deg);
            animation: adminFloat 19s ease-in-out infinite;
        }
        
        @keyframes adminBgShift {
            0%, 100% {
                transform: translate(0, 0);
            }
            50% {
                transform: translate(-20px, -20px);
            }
        }
        
        @keyframes adminFloat {
            0%, 100% {
                transform: translate(0, 0) rotate(var(--rotation, 0deg));
            }
            25% {
                transform: translate(20px, -20px) rotate(calc(var(--rotation, 0deg) + 5deg));
            }
            50% {
                transform: translate(-10px, 20px) rotate(calc(var(--rotation, 0deg) - 5deg));
            }
            75% {
                transform: translate(15px, 10px) rotate(calc(var(--rotation, 0deg) + 3deg));
            }
        }
        
        /* Particle/Star Effect for Admin - RESTORED */
        .admin-particles {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            overflow: hidden;
            pointer-events: none;
            z-index: 0;
        }
        
        .admin-particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: linear-gradient(135deg, #e879f9, #c084fc);
            border-radius: 50%;
            opacity: 0;
            animation: adminFloatParticle 20s infinite linear;
            box-shadow: 0 0 6px rgba(232, 121, 249, 0.8);
        }
        
        @keyframes adminFloatParticle {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 0;
            }
            5% {
                opacity: 1;
            }
            95% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100vh) rotate(360deg);
                opacity: 0;
            }
        }
        
        .admin-particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: linear-gradient(135deg, #e879f9, #c084fc);
            border-radius: 50%;
            opacity: 0;
            animation: adminFloatParticle 20s infinite linear;
            box-shadow: 0 0 6px rgba(232, 121, 249, 0.8);
        }
        
        @keyframes adminFloatParticle {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 0;
            }
            5% {
                opacity: 1;
            }
            95% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100vh) rotate(360deg);
                opacity: 0;
            }
        }
        
        .admin-container {
            position: relative;
            max-width: 1400px;
            margin: 0 auto;
            z-index: 1;
            width: 100%;
            box-sizing: border-box;
        }
        
        /* Table styles */
        .admin-table-container {
            overflow-x: auto;
            margin: 30px 0;
        }
        
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            background: linear-gradient(135deg, rgba(15, 15, 18, 0.97) 0%, rgba(18, 18, 22, 0.97) 100%);
            border: 2px solid rgba(232, 121, 249, 0.5);
            border-radius: 15px;
            overflow: hidden;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
        }
        
        .admin-table thead {
            background: linear-gradient(135deg, rgba(147, 51, 234, 0.9) 0%, rgba(232, 121, 249, 0.85) 100%);
        }
        
        .admin-table th {
            padding: 20px;
            text-align: left;
            color: #ffffff;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }
        
        .admin-table td {
            padding: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            color: #e0e0e0;
            background: rgba(15, 15, 18, 0.25);
        }
        
        .admin-table tbody tr:nth-child(even) {
            background: rgba(18, 18, 22, 0.25);
        }
        
        .admin-table tbody tr:hover {
            background: rgba(147, 51, 234, 0.4) !important;
            transform: scale(1.01);
            transition: all 0.2s ease;
        }
        
        .actions-cell {
            display: flex;
            gap: 10px;
        }
        
        /* Form styles */
        .admin-form-container {
            max-width: 800px;
            margin: 0 auto;
            background: linear-gradient(135deg, rgba(18, 18, 22, 0.7) 0%, rgba(15, 15, 18, 0.6) 100%);
            border: 1px solid rgba(232, 121, 249, 0.2);
            border-radius: 20px;
            padding: 40px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }
        
        .admin-form-group {
            margin-bottom: 25px;
        }
        
        .admin-form-group label {
            display: block;
            margin-bottom: 10px;
            color: #e879f9;
            font-weight: 500;
            font-size: 14px;
        }
        
        .admin-form-group input,
        .admin-form-group textarea,
        .admin-form-group select {
            width: 100%;
            padding: 15px 20px;
            background: var(--carbon-dark);
            border: 1px solid var(--metal-dark);
            border-radius: 10px;
            color: #ffffff;
            font-size: 16px;
            font-family: inherit;
            transition: all 0.3s ease;
        }


        
        .admin-form-group select option {
            background: var(--carbon-dark);
            color: #ffffff;
        }

        .admin-form-group input:focus,
        .admin-form-group textarea:focus,
        .admin-form-group select:focus {
            outline: none;
            border-color: #e879f9;
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 20px rgba(232, 121, 249, 0.2);
        }
        
        .admin-form-group textarea {
            resize: vertical;
            min-height: 120px;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .admin-header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .admin-header-section h2 {
            font-size: 32px;
            background: linear-gradient(135deg, #ffffff 0%, #e879f9 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Admin Cards - Beautiful Styling */
        .admin-card {
            background: linear-gradient(135deg, rgba(18, 18, 22, 0.75) 0%, rgba(15, 15, 18, 0.65) 100%) !important;
            border: 1px solid rgba(232, 121, 249, 0.25) !important;
            border-radius: 20px !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(232, 121, 249, 0.1) inset;
            transition: all 0.3s ease;
        }
        
        .admin-card:hover {
            border-color: rgba(232, 121, 249, 0.4) !important;
            box-shadow: 0 12px 40px rgba(232, 121, 249, 0.25), 0 0 0 1px rgba(232, 121, 249, 0.2) inset;
            transform: translateY(-2px);
        }
        
        /* Dashboard grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .dashboard-card {
            background: linear-gradient(135deg, rgba(18, 18, 22, 0.7) 0%, rgba(15, 15, 18, 0.6) 100%);
            border: 1px solid rgba(232, 121, 249, 0.2);
            border-radius: 20px;
            padding: 30px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(10px);
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            border-color: rgba(232, 121, 249, 0.4);
            background: linear-gradient(135deg, rgba(20, 20, 24, 0.75) 0%, rgba(18, 18, 22, 0.65) 100%);
            box-shadow: 0 12px 40px rgba(232, 121, 249, 0.2);
        }
        
        .dashboard-card h2 {
            font-size: 24px;
            margin-bottom: 15px;
            color: #e879f9;
        }
        
        .dashboard-card p {
            color: #a0a0a0;
            margin-bottom: 20px;
        }
        
        .dashboard-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        /* Buttons */
        .btn {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #9333ea, #c084fc);
            color: white;
            box-shadow: 0 4px 15px rgba(147, 51, 234, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(147, 51, 234, 0.4);
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
        }
        
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        /* Admin Buttons - Modern Beautiful Styling */
        .btn-admin {
            padding: 10px 20px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        .btn-admin::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .btn-admin:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .btn-admin:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }
        
        .btn-admin:active {
            transform: translateY(0);
        }
        
        /* Add Button - Purple/Blue Gradient */
        .btn-admin.btn-add {
            background: linear-gradient(135deg, #9333ea 0%, #6366f1 50%, #3b82f6 100%);
            color: #ffffff;
            border: 1px solid rgba(147, 51, 234, 0.3);
            padding: 12px 24px;
            font-size: 13px;
            box-shadow: 0 4px 20px rgba(147, 51, 234, 0.4), 
                        0 0 0 1px rgba(147, 51, 234, 0.1) inset;
        }
        
        .btn-admin.btn-add:hover {
            background: linear-gradient(135deg, #a855f7 0%, #818cf8 50%, #60a5fa 100%);
            box-shadow: 0 6px 30px rgba(147, 51, 234, 0.5),
                        0 0 0 1px rgba(147, 51, 234, 0.2) inset;
            transform: translateY(-3px);
        }
        
        .btn-admin.btn-add i {
            font-size: 14px;
        }
        
        /* Edit/View Button - Blue Gradient */
        .btn-admin.btn-edit {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(96, 165, 250, 0.15) 100%);
            color: #60a5fa;
            border: 1.5px solid rgba(59, 130, 246, 0.4);
            padding: 8px 14px;
            font-size: 12px;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(59, 130, 246, 0.2),
                        0 0 0 1px rgba(59, 130, 246, 0.1) inset;
        }
        
        .btn-admin.btn-edit:hover {
            background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);
            color: #ffffff;
            border-color: rgba(59, 130, 246, 0.6);
            box-shadow: 0 4px 20px rgba(59, 130, 246, 0.4),
                        0 0 0 1px rgba(59, 130, 246, 0.2) inset;
            transform: translateY(-2px);
        }
        
        .btn-admin.btn-edit i {
            font-size: 13px;
        }
        
        /* Delete Button - Red Gradient */
        .btn-admin.btn-delete {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(248, 113, 113, 0.15) 100%);
            color: #f87171;
            border: 1.5px solid rgba(239, 68, 68, 0.4);
            padding: 8px 14px;
            font-size: 12px;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(239, 68, 68, 0.2),
                        0 0 0 1px rgba(239, 68, 68, 0.1) inset;
        }
        
        .btn-admin.btn-delete:hover {
            background: linear-gradient(135deg, #ef4444 0%, #f87171 100%);
            color: #ffffff;
            border-color: rgba(239, 68, 68, 0.6);
            box-shadow: 0 4px 20px rgba(239, 68, 68, 0.4),
                        0 0 0 1px rgba(239, 68, 68, 0.2) inset;
            transform: translateY(-2px);
        }
        
        .btn-admin.btn-delete i {
            font-size: 13px;
        }
        
        /* Icon-only buttons (small square buttons) */
        .btn-admin.btn-edit[style*="min-width: 40px"],
        .btn-admin.btn-edit[style*="min-width:40px"],
        .btn-admin[style*="min-width: 40px"] {
            padding: 0 !important;
            min-width: 40px !important;
            height: 40px !important;
            border-radius: 10px !important;
            width: 40px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        
        .btn-admin.btn-edit[style*="min-width: 40px"]:hover,
        .btn-admin.btn-edit[style*="min-width:40px"]:hover,
        .btn-admin[style*="min-width: 40px"]:hover {
            transform: translateY(-2px) scale(1.05);
        }
        
        /* Action buttons container spacing */
        .admin-table td > div[style*="display: flex"] {
            gap: 8px !important;
        }
        
        /* Button icons spacing */
        .btn-admin i {
            transition: transform 0.3s ease;
        }
        
        .btn-admin:hover i {
            transform: scale(1.1);
        }
        
        .no-items {
            text-align: center;
            padding: 60px 20px;
            color: #a0a0a0;
            font-size: 18px;
        }
        
        /* Modal Styles - Beautiful Glassmorphism */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(10px);
            z-index: 10000;
            display: none !important;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }
        
        .modal.active,
        .modal[style*="display: flex"],
        .modal[style*="display:flex"],
        .modal[style*="display: flex !important"],
        .modal[style*="display:flex !important"] {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        .modal-content {
            background: linear-gradient(135deg, rgba(15, 15, 18, 0.99) 0%, rgba(18, 18, 22, 0.99) 50%, rgba(20, 20, 24, 0.99) 100%);
            border: 2px solid rgba(232, 121, 249, 0.3);
            border-radius: 20px;
            padding: 0;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5),
                        0 0 0 1px rgba(232, 121, 249, 0.1) inset;
            backdrop-filter: blur(20px);
            animation: slideUp 0.3s ease;
            position: relative;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 25px 30px;
            border-bottom: 1px solid rgba(232, 121, 249, 0.2);
            background: linear-gradient(135deg, rgba(232, 121, 249, 0.1) 0%, rgba(0, 168, 255, 0.1) 100%);
            border-radius: 20px 20px 0 0;
        }
        
        .modal-header h2 {
            color: #ffffff;
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .modal-header h2 i {
            color: #e879f9;
        }
        
        .close-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            font-size: 20px;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
        }
        
        .close-btn:hover {
            background: rgba(239, 68, 68, 0.2);
            border-color: rgba(239, 68, 68, 0.4);
            color: #f87171;
            transform: rotate(90deg);
        }
        
        .modal-content > form,
        .modal-content > div {
            padding: 30px;
        }
        
        /* Form Groups in Modal */
        .modal .form-group {
            margin-bottom: 20px;
        }
        
        .modal .form-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #e879f9;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }
        
        .modal .form-group label i {
            font-size: 14px;
        }
        
        .modal .form-control {
            width: 100%;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            color: #ffffff;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .modal .form-control:focus {
            outline: none;
            border-color: rgba(232, 121, 249, 0.5);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 20px rgba(232, 121, 249, 0.2);
        }
        
        .modal .form-control::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }
        
        /* Search Input Styling - Modern Template */
        .form-control {
            width: 100%;
            padding: 12px 15px 12px 45px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.05) 100%);
            border: 1.5px solid rgba(232, 121, 249, 0.3);
            border-radius: 12px;
            color: #ffffff;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2),
                        0 0 0 1px rgba(232, 121, 249, 0.1) inset;
        }
        
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
            font-weight: 400;
        }
        
        .form-control:focus {
            outline: none;
            border-color: rgba(232, 121, 249, 0.6);
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.12) 0%, rgba(255, 255, 255, 0.08) 100%);
            box-shadow: 0 4px 20px rgba(232, 121, 249, 0.3),
                        0 0 0 1px rgba(232, 121, 249, 0.2) inset;
            transform: translateY(-1px);
        }
        
        .form-control:hover:not(:focus) {
            border-color: rgba(232, 121, 249, 0.4);
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.06) 100%);
        }
        
        /* Search Input Container with Icon */
        .search-input-container {
            position: relative;
            display: inline-block;
        }
        
        .search-input-container .fa-search,
        .search-input-container i.fas.fa-search {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(232, 121, 249, 0.6);
            font-size: 14px;
            pointer-events: none;
            transition: all 0.3s ease;
            z-index: 1;
        }
        
        .search-input-container .form-control:focus + .fa-search,
        .search-input-container .form-control:focus ~ i.fas.fa-search,
        .search-input-container:has(.form-control:focus) .fa-search,
        .search-input-container:has(.form-control:focus) i.fas.fa-search {
            color: #e879f9;
            transform: translateY(-50%) scale(1.1);
        }
        
        /* Alternative selector for browsers that don't support :has() */
        .search-input-container .form-control:focus ~ i {
            color: #e879f9;
        }
        
        /* Mobile Menu Toggle Button */
        .mobile-menu-toggle {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1001;
            background: rgba(232, 121, 249, 0.2);
            border: 1px solid rgba(232, 121, 249, 0.4);
            color: #e879f9;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 20px;
            display: none;
        }
        
        .mobile-menu-toggle:hover {
            background: rgba(232, 121, 249, 0.3);
        }
        
        @media (max-width: 992px) {
            .admin-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                box-shadow: 2px 0 10px rgba(0, 0, 0, 0.5);
            }
            
            .admin-sidebar.mobile-open {
                transform: translateX(0);
            }
            
            .admin-content {
                margin-left: 0;
                width: 100%;
                max-width: 100vw;
                padding: 100px 20px 40px;
            }
            
            .admin-top-header {
                left: 0;
                width: 100%;
            }
            
            .mobile-menu-toggle {
                display: block;
            }
        }
        
        @media (max-width: 768px) {
            .admin-content {
                padding: 100px 15px 30px;
                width: 100%;
                max-width: 100vw;
            }
            
            .admin-header-section {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
            }
            
            .actions-cell {
                flex-direction: column;
            }
            
            .sidebar-nav-item {
                padding: 12px 20px;
                font-size: 13px;
            }
            
            .admin-container {
                padding: 0 10px;
            }
        }
        
        /* Fix for zoom out issues */
        @media (max-width: 1400px) {
            .admin-container {
                max-width: 100%;
                padding: 0 20px;
            }
        }
        
        /* Prevent layout breaking on zoom */
        html {
            overflow-x: hidden;
            min-width: 320px;
        }
        
        * {
            box-sizing: border-box;
        }
        
        /* Fix table overflow on zoom */
        .admin-table-container {
            overflow-x: auto;
            width: 100%;
            -webkit-overflow-scrolling: touch;
        }
        
        .admin-table {
            min-width: 800px;
            width: 100%;
        }
        
        /* Loading Screen Styles for Admin - Prism Loader from Back Template */
        /* Admin Page Transition Loader - Fast & Discreet */
        .page-transition-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: rgba(10, 10, 12, 0.95);
            backdrop-filter: blur(10px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 10001;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .page-transition-loader.show {
            display: flex;
            opacity: 1;
        }
        
        .page-transition-loader.hide {
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .admin-transition-spinner {
            width: 50px;
            height: 50px;
            border: 3px solid rgba(232, 121, 249, 0.2);
            border-top-color: #e879f9;
            border-right-color: #c084fc;
            border-radius: 50%;
            animation: adminSpin 0.6s linear infinite;
            box-shadow: 0 0 20px rgba(232, 121, 249, 0.3);
        }
        
        @keyframes adminSpin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Old loader styles removed - keeping for backward compatibility */
        .page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: var(--primary-black, #0a0a0a);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 10000;
            opacity: 0;
            visibility: hidden;
        }
        
        .page-loader.hidden {
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
            border-image: linear-gradient(45deg, var(--accent-red, #ff3333), var(--accent-blue, #00a8ff)) 1;
            animation-delay: 0s;
        }
        
        .prism-face:nth-child(2) {
            border-image: linear-gradient(45deg, var(--accent-blue, #00a8ff), var(--accent-green, #00ff88)) 1;
            transform: rotate(60deg);
            animation-delay: 0.2s;
        }
        
        .prism-face:nth-child(3) {
            border-image: linear-gradient(45deg, var(--accent-green, #00ff88), var(--accent-purple, #9945ff)) 1;
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
        
        .loader-text {
            color: var(--accent-purple, #9945ff);
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 3px;
            font-weight: 600;
        }
        
        /* Nav Actions (Public Site & Logout Buttons) */
        .nav-actions {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-left: 20px;
        }
        
        .nav-public-site-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: transparent;
            border: 1px solid var(--accent-blue);
            color: var(--accent-blue);
            border-radius: 8px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        
        .nav-public-site-btn:hover {
            background: var(--accent-blue);
            color: var(--text-primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 168, 255, 0.3);
            }
        
        .nav-public-site-btn .public-icon {
            font-size: 16px;
        }
        
        .nav-public-site-btn .public-text {
            display: inline;
        }
        
        .nav-logout-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: transparent;
            border: 1px solid var(--accent-red);
            color: var(--accent-red);
            border-radius: 8px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        
        .nav-logout-btn:hover {
            background: var(--accent-red);
            color: var(--text-primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 51, 51, 0.3);
        }
        
        .nav-logout-btn .logout-icon {
            font-size: 16px;
        }
        
        .nav-logout-btn .logout-text {
            display: inline;
        }
        
        /* Logout link in nav menu */
        .nav-logout-item {
            display: none;
        }
        
        .nav-logout-link {
            color: var(--accent-red) !important;
            border: 1px solid var(--accent-red);
            border-radius: 8px;
            margin: 10px 20px;
            padding: 12px 20px !important;
            display: block;
            text-align: center;
        }
        
        .nav-logout-link:hover {
            background: var(--accent-red);
            color: var(--text-primary) !important;
        }
        
        /* Public site link in nav menu for mobile */
        .nav-public-site-item {
            display: none;
        }
        
        .nav-public-site-link {
            color: var(--accent-blue) !important;
            border: 1px solid var(--accent-blue);
            border-radius: 8px;
            margin: 10px 20px;
            padding: 12px 20px !important;
            display: block;
            text-align: center;
        }
        
        .nav-public-site-link:hover {
            background: var(--accent-blue);
            color: var(--text-primary) !important;
        }
        
        @media (max-width: 992px) {
            .nav-actions {
                display: none;
            }
            
            .nav-public-site-item {
                display: block;
                border-top: 1px solid rgba(255, 255, 255, 0.1);
                margin-top: 10px;
                padding-top: 10px;
            }
            
            .nav-logout-item {
                display: block;
                border-top: 1px solid rgba(255, 255, 255, 0.1);
                margin-top: 10px;
                padding-top: 10px;
            }
        }
        /* Force hide spider web shapes only - particles and background animation restored */
        .admin-content .admin-shapes,
        .admin-content .admin-shapes .admin-shape,
        .admin-content .admin-shapes .admin-shape.shape1,
        .admin-content .admin-shapes .admin-shape.shape2,
        .admin-content .admin-shapes .admin-shape.shape3,
        .admin-content .admin-shapes .admin-shape.shape4,
        .admin-content .admin-shapes .admin-shape.shape5,
        .admin-content .admin-shapes .admin-shape.shape6 {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            width: 0 !important;
            height: 0 !important;
            overflow: hidden !important;
        }
    </style>
</head>
<body>
    <!-- Loading Screen - Prism Loader from Back Template -->
    <!-- Admin Page Transition Loader -->
    <div id="page-transition-loader" class="page-transition-loader">
        <div class="admin-transition-spinner"></div>
    </div>
    
    <!-- Old loader kept hidden for backward compatibility -->
    <div id="page-loader" class="page-loader hidden" style="display: none;">
        <div class="loader-content">
            <div class="loader-prism">
                <div class="prism-face"></div>
                <div class="prism-face"></div>
                <div class="prism-face"></div>
            </div>
            <div class="loader-text">Chargement Admin...</div>
            </div>
        </div>
    
    <?php
    // Get admin information
    $adminUsername = $_SESSION['username'] ?? 'Admin';
    $adminUserId = $_SESSION['user_id'] ?? null;
    $adminAvatar = $_SESSION['avatar'] ?? null;
    $adminInitial = strtoupper(substr($adminUsername, 0, 1));
    
    // Try to get admin name from database if available
    $adminName = $adminUsername;
    if ($adminUserId) {
        try {
            if (!isset($db)) {
                require_once "config/database.php";
                $database = new Database();
                $db = $database->getConnection();
            }
            require_once "models/User.php";
            $userModel = new User($db);
            $userModel->id = $adminUserId;
            if ($userModel->readOne()) {
                $adminName = $userModel->username ?? $adminUsername;
                $adminAvatar = $userModel->avatar ?? $adminAvatar;
                $adminInitial = strtoupper(substr($adminName, 0, 1));
            }
        } catch (Exception $e) {
            error_log("Error fetching admin info: " . $e->getMessage());
        }
    }
    ?>
    
    <div class="admin-wrapper">
        <!-- Left Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <img src="public/images/logo.png" alt="Logo" onerror="this.style.display='none'; this.parentElement.innerHTML='<div style=\'width: 40px; height: 40px; border-radius: 8px; background: linear-gradient(135deg, #9333ea, #c084fc); display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 20px; color: white;\'>M</div>';">
                </div>
                <div class="sidebar-title">Admin System</div>
            </div>
            
            <nav class="sidebar-nav">
                <a href="?action=admin_dashboard" class="sidebar-nav-item <?php echo (isset($currentPage) && $currentPage == 'admin') ? 'active' : ''; ?>">
                    📊 Dashboard
                </a>
                <a href="?action=admin_users" class="sidebar-nav-item <?php echo (isset($currentPage) && $currentPage == 'users') ? 'active' : ''; ?>">
                    👥 Utilisateurs
                </a>
                <a href="?action=admin_games" class="sidebar-nav-item <?php echo (isset($currentPage) && $currentPage == 'games') ? 'active' : ''; ?>">
                    🎮 Jeux
                </a>
                <a href="?action=admin_search_games" class="sidebar-nav-item <?php echo (isset($currentPage) && $currentPage == 'admin_search_games') ? 'active' : ''; ?>">
                    🎮 Recherche Jeux
                </a>
                <a href="?controller=formation&action=adminList" class="sidebar-nav-item <?php echo (isset($currentPage) && $currentPage == 'formations') ? 'active' : ''; ?>">
                    📚 Formations
                </a>
                <a href="?controller=education&action=adminList" class="sidebar-nav-item <?php echo (isset($currentPage) && $currentPage == 'educations') ? 'active' : ''; ?>">
                    🎓 Éducations
                </a>
                <a href="?controller=category&action=adminSearch" class="sidebar-nav-item <?php echo (isset($currentPage) && $currentPage == 'search') ? 'active' : ''; ?>">
                    🔍 Recherche Education Formation
                </a>
                <a href="?action=admin_projects" class="sidebar-nav-item <?php echo (isset($currentPage) && $currentPage == 'admin_projects') ? 'active' : ''; ?>">
                    🌍 Projets
                </a>
                <a href="?action=admin_donations" class="sidebar-nav-item <?php echo (isset($currentPage) && ($currentPage == 'admin_donations' || $currentPage == 'donations')) ? 'active' : ''; ?>">
                    💝 Donations
                </a>
                <a href="?action=admin_events" class="sidebar-nav-item <?php echo (isset($currentPage) && $currentPage == 'admin_events') ? 'active' : ''; ?>">
                    📅 Événements
                </a>
                <a href="?action=admin_participations" class="sidebar-nav-item <?php echo (isset($currentPage) && $currentPage == 'admin_participations') ? 'active' : ''; ?>">
                    👥 Participations
                </a>
                <a href="?action=admin_reclamations" class="sidebar-nav-item <?php echo (isset($currentPage) && $currentPage == 'admin_reclamations') ? 'active' : ''; ?>">
                    📋 Réclamations
                </a>
                
                <div style="margin: 20px 0; border-top: 1px solid rgba(255,255,255,0.05);"></div>
                
                <!-- Tests (kept as system utilities) -->
                <a href="?controller=adminTest&action=listRequests" class="sidebar-nav-item <?php echo (isset($currentPage) && in_array($currentPage, ['test_requests', 'test_attempts', 'test_questions'])) ? 'active' : ''; ?>">
                    📝 Demandes de Test
                </a>
                <a href="?controller=adminTest&action=listAttempts" class="sidebar-nav-item <?php echo (isset($currentPage) && $currentPage == 'test_attempts') ? 'active' : ''; ?>" style="padding-left: 50px; font-size: 0.9em;">
                    ➤ Tentatives
                </a>
                <a href="?controller=adminTest&action=manageQuestions" class="sidebar-nav-item <?php echo (isset($currentPage) && $currentPage == 'test_questions') ? 'active' : ''; ?>" style="padding-left: 50px; font-size: 0.9em;">
                    ➤ Questions QCM
                </a>
                
                <!-- System -->
                <a href="?action=profile" class="sidebar-nav-item <?php echo (isset($currentPage) && $currentPage == 'profile') ? 'active' : ''; ?>">
                    👤 Mon Profil
                </a>
                <a href="?controller=formation&action=userDashboard" class="sidebar-nav-item">
                    🌐 Site Public
                </a>
            </nav>
            
            <div class="sidebar-footer">
                <a href="?action=logout&redirect=home" 
                   class="sidebar-logout-btn" 
                   onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?')">
                    🚪 Déconnexion
                </a>
            </div>
        </aside>
        
        <!-- Mobile Menu Toggle -->
        <button class="mobile-menu-toggle" id="mobileMenuToggle" onclick="toggleSidebar()" style="display: none;">
            ☰
        </button>
        
        <!-- Top Header Bar -->
        <header class="admin-top-header">
            <div class="admin-profile-section" onclick="showAdminProfileModal()" style="cursor: pointer; transition: all 0.3s ease;" onmouseover="this.style.opacity='0.8';" onmouseout="this.style.opacity='1';">
                <div class="admin-profile-info">
                    <div class="admin-profile-name"><?php echo htmlspecialchars($adminName); ?></div>
                    <div class="admin-profile-role">Administrateur</div>
            </div>
                <div class="admin-profile-avatar">
                    <?php echo htmlspecialchars($adminInitial); ?>
                </div>
            </div>
    </header>
    
    <!-- Modal Profil Admin -->
    <div class="modal" id="adminProfileModal" style="display: none;">
        <div class="modal-content" style="max-width: 600px; max-height: 90vh; overflow-y: auto; background: linear-gradient(135deg, rgba(15, 15, 18, 0.98) 0%, rgba(18, 18, 22, 0.98) 100%); border: 1px solid rgba(232, 121, 249, 0.3); border-radius: 20px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5); position: relative;">
            <button class="close-btn" onclick="hideAdminProfileModal()" style="position: absolute; top: 20px; right: 20px; z-index: 10; background: rgba(255, 255, 255, 0.1); border: none; color: #fff; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; font-size: 24px; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;" onmouseover="this.style.background='rgba(255, 255, 255, 0.2)';" onmouseout="this.style.background='rgba(255, 255, 255, 0.1)';" title="Fermer">×</button>
            
            <div style="padding: 40px 30px 30px; text-align: center; border-bottom: 1px solid rgba(232, 121, 249, 0.2);">
                <?php
                // Récupérer l'avatar de l'admin
                $adminAvatarUrl = null;
                $adminAvatarColor = '#' . substr(md5($adminName), 0, 6);
                
                if (!empty($adminAvatar)) {
                    $avatarPath = trim($adminAvatar);
                    $avatarPath = ltrim($avatarPath, '/');
                    $avatarPath = str_replace('projet01/', '', $avatarPath);
                    
                    if (strpos($avatarPath, 'public/') === 0) {
                        $adminAvatarUrl = $avatarPath;
                    } else {
                        $adminAvatarUrl = 'public/' . ltrim($avatarPath, '/');
                    }
                }
                ?>
                <div style="position: relative; display: inline-block; margin-bottom: 20px;">
                    <?php if (!empty($adminAvatarUrl) && file_exists($adminAvatarUrl)): ?>
                        <img src="<?php echo htmlspecialchars($adminAvatarUrl); ?>" 
                             alt="Avatar Admin" 
                             style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid rgba(232, 121, 249, 0.5); box-shadow: 0 0 30px rgba(232, 121, 249, 0.3);"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div style="display: none; width: 120px; height: 120px; border-radius: 50%; background: <?php echo $adminAvatarColor; ?>; align-items: center; justify-content: center; margin: 0 auto; border: 4px solid rgba(232, 121, 249, 0.5); box-shadow: 0 0 30px rgba(232, 121, 249, 0.3); position: absolute; top: 0; left: 50%; transform: translateX(-50%);">
                            <span style="color: #ffffff; font-size: 50px; font-weight: bold; text-shadow: 0 2px 10px rgba(0,0,0,0.5);"><?php echo htmlspecialchars($adminInitial); ?></span>
                        </div>
                    <?php else: ?>
                        <div style="width: 120px; height: 120px; border-radius: 50%; background: <?php echo $adminAvatarColor; ?>; display: flex; align-items: center; justify-content: center; margin: 0 auto; border: 4px solid rgba(232, 121, 249, 0.5); box-shadow: 0 0 30px rgba(232, 121, 249, 0.3);">
                            <span style="color: #ffffff; font-size: 50px; font-weight: bold; text-shadow: 0 2px 10px rgba(0,0,0,0.5);"><?php echo htmlspecialchars($adminInitial); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <h2 style="color: #ffffff; font-size: 28px; font-weight: 700; margin: 0 0 8px 0; text-shadow: 0 0 20px rgba(232, 121, 249, 0.5);">
                    <?php echo htmlspecialchars($adminName); ?>
                </h2>
                <p style="color: rgba(232, 121, 249, 0.8); font-size: 14px; margin: 0 0 5px 0; text-transform: uppercase; letter-spacing: 1px;">
                    @<?php echo htmlspecialchars($adminUsername); ?>
                </p>
                <span style="display: inline-block; background: linear-gradient(135deg, rgba(232, 121, 249, 0.2), rgba(147, 51, 234, 0.2)); color: rgba(232, 121, 249, 1); padding: 6px 16px; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1px solid rgba(232, 121, 249, 0.3); margin-top: 10px;">
                    👑 Administrateur
                </span>
            </div>
            
            <!-- Section Informations du Compte -->
            <div style="padding: 30px;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; color: rgba(232, 121, 249, 1); font-size: 18px; font-weight: 600;">
                    <i class="fas fa-info-circle"></i>
                    <span>Informations du Compte</span>
                </div>
                
                <div style="display: grid; gap: 15px;">
                    <div style="background: rgba(42, 42, 42, 0.3); padding: 15px 20px; border-radius: 12px; border-left: 3px solid rgba(232, 121, 249, 0.5);">
                        <div style="color: rgba(255, 255, 255, 0.6); font-size: 12px; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.5px;">Rôle</div>
                        <div style="color: #ffffff; font-size: 16px; font-weight: 600;">Administrateur</div>
                    </div>
                    
                    <div style="background: rgba(42, 42, 42, 0.3); padding: 15px 20px; border-radius: 12px; border-left: 3px solid rgba(0, 255, 204, 0.5);">
                        <div style="color: rgba(255, 255, 255, 0.6); font-size: 12px; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.5px;">Statut</div>
                        <div style="color: #00ffcc; font-size: 16px; font-weight: 600;">
                            <span style="background: rgba(0, 255, 204, 0.2); color: #00ffcc; padding: 4px 12px; border-radius: 12px; font-size: 12px; border: 1px solid rgba(0, 255, 204, 0.3);">✓ Actif</span>
                        </div>
                    </div>
                    
                    <?php if ($adminUserId): ?>
                    <div style="background: rgba(42, 42, 42, 0.3); padding: 15px 20px; border-radius: 12px; border-left: 3px solid rgba(255, 215, 0, 0.5);">
                        <div style="color: rgba(255, 255, 255, 0.6); font-size: 12px; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.5px;">ID Utilisateur</div>
                        <div style="color: #ffd700; font-size: 16px; font-weight: 600;">#<?php echo htmlspecialchars($adminUserId); ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php
                    // Récupérer la date d'inscription si disponible
                    $adminCreatedAt = null;
                    if ($adminUserId) {
                        try {
                            if (!isset($db)) {
                                require_once __DIR__ . '/../../config/database.php';
                                $database = new Database();
                                $db = $database->getConnection();
                            }
                            $query = "SELECT created_at FROM users WHERE id = ?";
                            $stmt = $db->prepare($query);
                            $stmt->execute([$adminUserId]);
                            $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
                            if ($userRow && !empty($userRow['created_at'])) {
                                $adminCreatedAt = $userRow['created_at'];
                            }
                        } catch (Exception $e) {
                            error_log("Error fetching admin created_at: " . $e->getMessage());
                        }
                    }
                    ?>
                    
                    <?php if ($adminCreatedAt): ?>
                    <div style="background: rgba(42, 42, 42, 0.3); padding: 15px 20px; border-radius: 12px; border-left: 3px solid rgba(255, 170, 0, 0.5);">
                        <div style="color: rgba(255, 255, 255, 0.6); font-size: 12px; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.5px;">Date d'inscription</div>
                        <div style="color: #ffaa00; font-size: 16px; font-weight: 600;"><?php echo date('d/m/Y', strtotime($adminCreatedAt)); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div style="margin-top: 30px; padding-top: 30px; border-top: 1px solid rgba(232, 121, 249, 0.2);">
                    <a href="?action=profile" style="display: inline-block; background: linear-gradient(135deg, rgba(232, 121, 249, 0.2), rgba(147, 51, 234, 0.2)); color: rgba(232, 121, 249, 1); padding: 12px 24px; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 14px; border: 1px solid rgba(232, 121, 249, 0.3); transition: all 0.3s ease; text-align: center; width: 100%; box-sizing: border-box;" onmouseover="this.style.background='linear-gradient(135deg, rgba(232, 121, 249, 0.3), rgba(147, 51, 234, 0.3))'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='linear-gradient(135deg, rgba(232, 121, 249, 0.2), rgba(147, 51, 234, 0.2))'; this.style.transform='translateY(0)';">
                        <i class="fas fa-user-edit" style="margin-right: 8px;"></i> Modifier mon profil
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function showAdminProfileModal() {
            const modal = document.getElementById('adminProfileModal');
            if (modal) {
                modal.style.display = 'flex';
                modal.style.alignItems = 'center';
                modal.style.justifyContent = 'center';
                document.body.style.overflow = 'hidden';
                
                // Animation d'apparition
                setTimeout(() => {
                    modal.style.opacity = '1';
                }, 10);
            }
        }
        
        function hideAdminProfileModal() {
            const modal = document.getElementById('adminProfileModal');
            if (modal) {
                modal.style.opacity = '0';
                setTimeout(() => {
                    modal.style.display = 'none';
                    document.body.style.overflow = '';
                }, 300);
            }
        }
        
        // Fermer le modal en cliquant en dehors
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('adminProfileModal');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        hideAdminProfileModal();
                    }
                });
            }
        });
    </script>

        <script>
            // Mobile sidebar toggle
            function toggleSidebar() {
                const sidebar = document.querySelector('.admin-sidebar');
                if (sidebar) {
                    sidebar.classList.toggle('mobile-open');
                }
            }
            
            // Show/hide mobile toggle based on screen size
            function checkMobile() {
                const toggle = document.getElementById('mobileMenuToggle');
                if (window.innerWidth <= 992) {
                    if (toggle) toggle.style.display = 'block';
                } else {
                    if (toggle) toggle.style.display = 'none';
                    const sidebar = document.querySelector('.admin-sidebar');
                    if (sidebar) sidebar.classList.remove('mobile-open');
                }
            }
            
            window.addEventListener('resize', checkMobile);
            checkMobile();
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(e) {
                if (window.innerWidth <= 992) {
                    const sidebar = document.querySelector('.admin-sidebar');
                    const toggle = document.getElementById('mobileMenuToggle');
                    if (sidebar && toggle && 
                        !sidebar.contains(e.target) && 
                        !toggle.contains(e.target) &&
                        sidebar.classList.contains('mobile-open')) {
                        sidebar.classList.remove('mobile-open');
                    }
                }
            });
        </script>


