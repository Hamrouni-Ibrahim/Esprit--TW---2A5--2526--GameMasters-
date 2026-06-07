<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Game Master'; ?></title>
    <link rel="stylesheet" href="public/css/templatemo-graph-page.css">
    <style>
        
        .logo-icon {
            width: 48px !important;
            height: 48px !important;
            border-radius: 50% !important;
            border: 1.5px solid rgba(0, 255, 204, 0.4) !important;
            background: rgba(0, 0, 0, 0.2) !important;
            backdrop-filter: blur(10px) !important;
            box-shadow: 
                0 0 15px rgba(0, 255, 204, 0.3),
                0 0 30px rgba(244, 114, 182, 0.2) !important;
            transition: all 0.3s ease !important;
            padding: 0 !important;
            position: relative !important;
            overflow: hidden !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        
        .logo-icon::before {
            display: none !important;
        }
        
        .logo-icon::after {
            display: none !important;
        }
        
        /* Additional shine layer */
        .logo {
            position: relative !important;
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            text-decoration: none !important;
            margin-right: auto !important;
            margin-left: -20px !important;
        }
        
        .logo::before {
            display: none !important;
        }
        
        @keyframes backgroundPulse {
            0%, 100% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
        }
        
        @keyframes medalGlow {
            0% {
                filter: drop-shadow(0 0 5px rgba(255, 215, 0, 0.5));
            }
            100% {
                filter: drop-shadow(0 0 15px rgba(255, 215, 0, 0.9));
            }
        }
        
        @keyframes boxPulse {
            0%, 100% {
                box-shadow: 
                    0 0 25px rgba(0, 200, 180, 0.25),
                    0 0 50px rgba(0, 160, 200, 0.2),
                    0 4px 20px rgba(0, 0, 0, 0.3),
                    inset 0 0 20px rgba(255, 255, 255, 0.08),
                    inset 0 2px 0 rgba(255, 255, 255, 0.15);
            }
            50% {
                box-shadow: 
                    0 0 35px rgba(0, 200, 180, 0.35),
                    0 0 70px rgba(0, 160, 200, 0.3),
                    0 4px 20px rgba(0, 0, 0, 0.3),
                    inset 0 0 25px rgba(255, 255, 255, 0.12),
                    inset 0 2px 0 rgba(255, 255, 255, 0.2);
            }
        }
        
        @keyframes floatAnimation {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-5px);
            }
        }
        
        @keyframes logoShine {
            0% {
                transform: translateX(-100%) translateY(-100%) rotate(45deg);
                opacity: 0;
            }
            20% {
                opacity: 1;
            }
            80% {
                opacity: 1;
            }
            100% {
                transform: translateX(100%) translateY(100%) rotate(45deg);
                opacity: 0;
            }
        }
        
        @keyframes borderFlow {
            0%, 100% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
        }
        
        @keyframes rotateShine {
            0% {
                transform: translate(-50%, -50%) rotate(0deg) scale(0.8);
                opacity: 0.3;
            }
            50% {
                opacity: 0.6;
            }
            100% {
                transform: translate(-50%, -50%) rotate(360deg) scale(1.2);
                opacity: 0.3;
            }
        }
        
        .logo-icon:hover {
            border-color: rgba(0, 255, 204, 0.6) !important;
            box-shadow: 
                0 0 20px rgba(0, 255, 204, 0.4),
                0 0 40px rgba(244, 114, 182, 0.3) !important;
            transform: scale(1.05) !important;
        }
        
        .logo-icon:hover::after {
            display: none !important;
        }
        
        .logo-icon:hover::before {
            display: none !important;
        }
        
        .logo:hover::before {
            display: none !important;
        }
        
        .logo-image {
            filter: brightness(1.1) contrast(1.1) !important;
            transition: all 0.3s ease !important;
            width: 100% !important;
            height: 100% !important;
            position: relative !important;
            z-index: 1 !important;
            display: block !important;
            object-fit: cover !important;
            border-radius: 50% !important;
        }
        
        .logo-fallback {
            width: 100% !important;
            height: 100% !important;
            display: none !important;
            border-radius: 50% !important;
        }
        
        .logo-icon .logo-fallback {
            border-radius: 50% !important;
        }
        
        .logo-icon:has(img:not([style*="display: none"])) .logo-fallback {
            display: none !important;
        }
        
        .logo-icon:not(:has(img:not([style*="display: none"]))) .logo-fallback {
            display: block !important;
        }
        
        .logo:hover .logo-image {
            filter: brightness(1.2) contrast(1.2) drop-shadow(0 0 12px rgba(0, 200, 180, 0.5)) drop-shadow(0 0 20px rgba(0, 160, 200, 0.4)) !important;
            transform: scale(1.05) !important;
            animation: imageGlow 2s ease-in-out infinite alternate !important;
        }
        
        @keyframes imageGlow {
            0% {
                filter: brightness(1.2) contrast(1.2) drop-shadow(0 0 12px rgba(0, 200, 180, 0.5)) drop-shadow(0 0 20px rgba(0, 160, 200, 0.4));
            }
            100% {
                filter: brightness(1.3) contrast(1.25) drop-shadow(0 0 16px rgba(0, 200, 180, 0.6)) drop-shadow(0 0 28px rgba(0, 160, 200, 0.5));
            }
        }
        
        .logo-text {
            font-size: 24px !important;
            font-weight: 500 !important;
            letter-spacing: 0.5px !important;
            display: flex !important;
            align-items: center !important;
            gap: 4px !important;
            position: relative !important;
            z-index: 1 !important;
            line-height: 1.2 !important;
        }
        
        .logo-text .prism {
            background: linear-gradient(135deg, #00ffcc 0%, #06b6d4 50%, #f472b6 100%) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            background-clip: text !important;
            font-weight: 500 !important;
            letter-spacing: 0.5px !important;
        }
        
        .logo-text .flux {
            background: linear-gradient(135deg, #06b6d4 0%, #f472b6 50%, #ec4899 100%) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            background-clip: text !important;
            font-weight: 500 !important;
            letter-spacing: 0.5px !important;
        }
        
        .logo:hover .logo-text {
            transition: all 0.3s ease !important;
        }
        
        /* Navbar Structure */
        #navbar {
            min-height: 80px !important;
            padding: 0 40px !important;
            display: flex !important;
            align-items: center !important;
        }
        
        .nav-container {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            width: 100% !important;
            max-width: 100% !important;
            gap: 15px !important;
            padding: 0 !important;
            flex-wrap: wrap !important;
        }
        
        /* Logo Block - Left */
        .logo-block {
            display: flex !important;
            align-items: center !important;
            flex-shrink: 0 !important;
            margin-right: auto !important;
        }
        
        /* Navigation Links Block - Center */
        .nav-links {
            display: flex !important;
            gap: 12px !important;
            list-style: none !important;
            align-items: center !important;
            flex-wrap: wrap !important;
            margin: 0 auto !important;
            padding: 0 !important;
            justify-content: center !important;
            flex: 1 !important;
        }
        
        .nav-links li {
            margin: 0 !important;
            padding: 0 !important;
            white-space: nowrap !important;
            display: flex !important;
            align-items: center !important;
            height: 40px !important;
            min-width: fit-content !important;
        }
        
        .nav-links a {
            display: inline-flex !important;
            align-items: center !important;
            padding: 8px 16px !important;
            margin: 0 !important;
            height: 40px !important;
            line-height: 1 !important;
            color: #ffffff !important;
            text-decoration: none !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
            position: relative !important;
            border-radius: 10px !important;
            background: rgba(255, 255, 255, 0.05) !important;
            backdrop-filter: blur(10px) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            overflow: hidden !important;
            box-sizing: border-box !important;
        }
        
        .nav-links a::before {
            content: '' !important;
            position: absolute !important;
            bottom: 0 !important;
            left: 50% !important;
            width: 0 !important;
            height: 3px !important;
            background: linear-gradient(90deg, #ff6b6b, #ff8e53, #ff6b6b) !important;
            background-size: 200% 100% !important;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
            transform: translateX(-50%) !important;
            border-radius: 2px !important;
            animation: gradientShift 3s ease infinite !important;
        }
        
        .nav-links a::after {
            content: '' !important;
            position: absolute !important;
            top: 0 !important;
            left: -100% !important;
            width: 100% !important;
            height: 100% !important;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent) !important;
            transition: left 0.5s ease !important;
        }
        
        @keyframes gradientShift {
            0%, 100% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
        }
        
        .nav-links a:hover {
            color: #ff8e53 !important;
            text-shadow: 0 0 15px rgba(255, 107, 107, 0.8), 0 0 25px rgba(255, 142, 83, 0.4) !important;
            background: rgba(255, 107, 107, 0.1) !important;
            border-color: rgba(255, 107, 107, 0.3) !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 20px rgba(255, 107, 107, 0.3), 0 0 30px rgba(255, 142, 83, 0.2) !important;
        }
        
        .nav-links a:hover::before {
            width: 100% !important;
            box-shadow: 0 0 20px rgba(255, 107, 107, 0.6) !important;
        }
        
        .nav-links a:hover::after {
            left: 100% !important;
        }
        
        .nav-links a.active {
            color: #00ffcc !important;
            background: rgba(0, 255, 204, 0.1) !important;
            border-color: rgba(0, 255, 204, 0.4) !important;
            text-shadow: 0 0 15px rgba(0, 255, 204, 0.8), 0 0 25px rgba(0, 204, 255, 0.4) !important;
            box-shadow: 0 4px 20px rgba(0, 255, 204, 0.2), inset 0 0 20px rgba(0, 255, 204, 0.05) !important;
        }
        
        .nav-links a.active::after {
            content: '' !important;
            position: absolute !important;
            bottom: 0 !important;
            left: 50% !important;
            width: 100% !important;
            height: 3px !important;
            background: linear-gradient(90deg, #00ffcc, #00ccff, #00ffcc) !important;
            background-size: 200% 100% !important;
            transform: translateX(-50%) !important;
            box-shadow: 0 0 20px rgba(0, 255, 204, 0.8), 0 0 40px rgba(0, 204, 255, 0.4) !important;
            border-radius: 2px !important;
            animation: gradientShift 3s ease infinite !important;
        }
        
        .nav-links a.active::before {
            display: none !important;
        }
        
        /* User Actions Block - Right */
        .user-actions {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            flex-shrink: 0 !important;
            margin-left: auto !important;
            flex-wrap: wrap !important;
        }
        
        .user-actions li {
            margin: 0 !important;
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
        }
        
        .user-actions a {
            display: inline-flex !important;
            align-items: center !important;
            padding: 8px 16px !important;
            height: 40px !important;
            line-height: 1 !important;
            color: #ffffff !important;
            text-decoration: none !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
            position: relative !important;
            border-radius: 10px !important;
            background: rgba(255, 255, 255, 0.05) !important;
            backdrop-filter: blur(10px) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            overflow: hidden !important;
            box-sizing: border-box !important;
        }
        
        .user-actions a::before {
            content: '' !important;
            position: absolute !important;
            bottom: 0 !important;
            left: 50% !important;
            width: 0 !important;
            height: 3px !important;
            background: linear-gradient(90deg, #ff6b6b, #ff8e53, #ff6b6b) !important;
            background-size: 200% 100% !important;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
            transform: translateX(-50%) !important;
            border-radius: 2px !important;
        }
        
        .user-actions a::after {
            content: '' !important;
            position: absolute !important;
            top: 0 !important;
            left: -100% !important;
            width: 100% !important;
            height: 100% !important;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent) !important;
            transition: left 0.5s ease !important;
        }
        
        .user-actions a:hover {
            color: #ff8e53 !important;
            text-shadow: 0 0 15px rgba(255, 107, 107, 0.8), 0 0 25px rgba(255, 142, 83, 0.4) !important;
            background: rgba(255, 107, 107, 0.1) !important;
            border-color: rgba(255, 107, 107, 0.3) !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 20px rgba(255, 107, 107, 0.3), 0 0 30px rgba(255, 142, 83, 0.2) !important;
        }
        
        .user-actions a:hover::before {
            width: 100% !important;
            box-shadow: 0 0 20px rgba(255, 107, 107, 0.6) !important;
        }
        
        .user-actions a:hover::after {
            left: 100% !important;
        }
        
        .user-actions a.active {
            color: #00ffcc !important;
            background: rgba(0, 255, 204, 0.1) !important;
            border-color: rgba(0, 255, 204, 0.4) !important;
            text-shadow: 0 0 15px rgba(0, 255, 204, 0.8), 0 0 25px rgba(0, 204, 255, 0.4) !important;
            box-shadow: 0 4px 20px rgba(0, 255, 204, 0.2), inset 0 0 20px rgba(0, 255, 204, 0.05) !important;
        }
        
        .user-actions a.active::after {
            content: '' !important;
            position: absolute !important;
            bottom: 0 !important;
            left: 50% !important;
            width: 100% !important;
            height: 3px !important;
            background: linear-gradient(90deg, #00ffcc, #00ccff, #00ffcc) !important;
            background-size: 200% 100% !important;
            transform: translateX(-50%) !important;
            box-shadow: 0 0 20px rgba(0, 255, 204, 0.8), 0 0 40px rgba(0, 204, 255, 0.4) !important;
            border-radius: 2px !important;
            animation: gradientShift 3s ease infinite !important;
        }
        
        .user-actions a.active::before {
            display: none !important;
        }
        
        /* Override for special buttons */
        .user-actions a.admin-btn {
            background: linear-gradient(135deg, #9333ea, #7c3aed) !important;
            color: white !important;
            font-weight: 600 !important;
            box-shadow: 0 4px 15px rgba(147, 51, 234, 0.3) !important;
            padding: 10px 20px !important;
            border-radius: 8px !important;
        }
        
        .user-actions a.admin-btn::before,
        .user-actions a.admin-btn::after {
            display: none !important;
        }
        
        .user-actions a.admin-btn:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 20px rgba(147, 51, 234, 0.4) !important;
            color: white !important;
            text-shadow: none !important;
            background: linear-gradient(135deg, #9333ea, #7c3aed) !important;
        }
        
        .user-actions a.cta-button {
            background: linear-gradient(135deg, #ff6b6b, #ff8e53) !important;
            color: white !important;
            font-weight: 600 !important;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3) !important;
            border-radius: 30px !important;
            padding: 10px 20px !important;
        }
        
        .user-actions a.cta-button::before,
        .user-actions a.cta-button::after {
            display: none !important;
        }
        
        .user-actions a.cta-button:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4) !important;
            color: white !important;
            text-shadow: none !important;
            background: linear-gradient(135deg, #ff6b6b, #ff8e53) !important;
        }
        
        /* Logout button special style */
        .user-actions a[href*="logout"] {
            color: #ff6b6b !important;
            font-weight: 600 !important;
        }
        
        .user-actions a[href*="logout"]:hover {
            color: #ff4757 !important;
            text-shadow: 0 0 10px rgba(255, 107, 107, 0.5) !important;
            background: rgba(255, 107, 107, 0.1) !important;
        }
        
        .user-actions a[href*="logout"]::before {
            background: linear-gradient(90deg, #ff6b6b, #ff4757) !important;
        }
        
        .nav-select {
            font-family: inherit !important;
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2300ffcc' d='M6 9L1 4h10z'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 10px center !important;
            background-size: 12px !important;
            padding: 8px 30px 8px 16px !important;
            height: 40px !important;
            border-radius: 10px !important;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            line-height: 1 !important;
            display: inline-flex !important;
            align-items: center !important;
            background-color: rgba(255, 255, 255, 0.05) !important;
            backdrop-filter: blur(10px) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #ffffff !important;
            cursor: pointer !important;
            position: relative !important;
            overflow: hidden !important;
            box-sizing: border-box !important;
            min-width: 120px !important;
        }
        
        .nav-select::before {
            content: '' !important;
            position: absolute !important;
            top: 0 !important;
            left: -100% !important;
            width: 100% !important;
            height: 100% !important;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent) !important;
            transition: left 0.5s ease !important;
        }
        
        .nav-select:hover {
            background-color: rgba(0, 255, 204, 0.1) !important;
            border-color: rgba(0, 255, 204, 0.4) !important;
            color: #00ffcc !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 20px rgba(0, 255, 204, 0.3), 0 0 30px rgba(0, 204, 255, 0.2) !important;
            text-shadow: 0 0 10px rgba(0, 255, 204, 0.5) !important;
        }
        
        .nav-select:hover::before {
            left: 100% !important;
        }
        
        .nav-select:focus {
            background-color: rgba(0, 255, 204, 0.15) !important;
            border-color: rgba(0, 255, 204, 0.6) !important;
            box-shadow: 0 0 20px rgba(0, 255, 204, 0.4), 0 4px 20px rgba(0, 255, 204, 0.3) !important;
            outline: none !important;
            color: #00ffcc !important;
        }
        
        .nav-select option {
            background: #1a1f3a !important;
            color: #00ffcc !important;
            padding: 12px !important;
            font-weight: 500 !important;
        }
        
        .nav-select option:hover {
            background: rgba(0, 255, 204, 0.2) !important;
        }
        
        
        /* Netflix-style Loading Screen (Intro Animation) */
        .page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: #000000;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10000;
            transition: opacity 0.8s ease, visibility 0.8s ease;
            overflow: hidden;
        }
        
        /* Page Transition Loader (Different from intro) */
        .page-transition-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: rgba(10, 14, 39, 0.95);
            backdrop-filter: blur(10px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
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
        
        .transition-spinner {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(0, 255, 204, 0.2);
            border-top-color: #00ffcc;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .page-loader.hidden {
            opacity: 0;
            visibility: hidden;
        }
        
        .loader-content {
            text-align: center;
            position: relative;
            width: 100%;
            height: 100%;
        }
        
        /* Animated particles/lines that move first */
        .animated-particles {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
        }
        
        .particle-line {
            position: absolute;
            width: 3px;
            height: 80px;
            background: linear-gradient(180deg, transparent, #00ffcc, transparent);
            opacity: 0;
            animation: particleMove 2s ease-out forwards;
        }
        
        .particle-circle {
            position: absolute;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: radial-gradient(circle, #00ffcc, transparent);
            box-shadow: 0 0 15px rgba(0, 255, 204, 0.8);
            opacity: 0;
            animation: particleMove 2s ease-out forwards;
        }
        
        @keyframes particleMove {
            0% {
                opacity: 0;
                transform: translate(0, 0) scale(0.5);
            }
            20% {
                opacity: 1;
            }
            80% {
                opacity: 1;
            }
            100% {
                opacity: 0;
                transform: translate(var(--end-x), var(--end-y)) scale(1.5);
            }
        }
        
        /* Energy waves converging to center */
        .energy-wave {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border: 2px solid rgba(0, 255, 204, 0.3);
            border-radius: 50%;
            animation: energyPulse 1.5s ease-out forwards;
        }
        
        .energy-wave:nth-child(1) {
            animation-delay: 0s;
            width: 200px;
            height: 200px;
        }
        
        .energy-wave:nth-child(2) {
            animation-delay: 0.2s;
            width: 300px;
            height: 300px;
        }
        
        .energy-wave:nth-child(3) {
            animation-delay: 0.4s;
            width: 400px;
            height: 400px;
        }
        
        @keyframes energyPulse {
            0% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(2);
                border-color: rgba(0, 255, 204, 0);
            }
            50% {
                opacity: 1;
                border-color: rgba(0, 255, 204, 0.6);
            }
            100% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0.3);
                border-color: rgba(0, 255, 204, 0);
            }
        }
        
        /* Logo appears after particles */
        .netflix-logo {
            width: 200px;
            height: 200px;
            margin: 0 auto;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0;
            animation: netflixEntrance 2s ease-out 1.8s forwards;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        /* Game Master text in animation (different class to avoid conflict) */
        .netflix-logo-text {
            margin-top: 20px;
            font-size: 36px;
            font-weight: 700;
            letter-spacing: 4px;
            background: linear-gradient(135deg, #00ffcc 0%, #00ccff 50%, #00ffcc 100%);
            background-size: 200% 100%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            opacity: 0;
            animation: textEntrance 2s ease-out 2s forwards, textShimmer 3s ease-in-out 2s infinite;
            text-shadow: 0 0 30px rgba(0, 255, 204, 0.5);
            position: relative;
            z-index: 4;
            text-transform: uppercase;
            filter: drop-shadow(0 0 20px rgba(0, 255, 204, 0.6));
        }
        
        @keyframes textEntrance {
            0% {
                opacity: 0;
                transform: translateY(30px) scale(0.8);
                filter: blur(10px);
            }
            40% {
                opacity: 1;
                transform: translateY(0) scale(1.05);
                filter: blur(0);
            }
            60% {
                transform: translateY(0) scale(1);
            }
            80% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
            100% {
                opacity: 0;
                transform: translateY(-20px) scale(0.9);
            }
        }
        
        @keyframes textShimmer {
            0%, 100% {
                background-position: 0% 50%;
                filter: drop-shadow(0 0 20px rgba(0, 255, 204, 0.6));
            }
            50% {
                background-position: 100% 50%;
                filter: drop-shadow(0 0 40px rgba(0, 255, 204, 1)) 
                        drop-shadow(0 0 60px rgba(0, 204, 255, 0.8));
            }
        }
        
        .netflix-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 0 30px rgba(0, 255, 204, 0.5));
            animation: logoGlow 2s ease-out 1.8s forwards;
            position: relative;
            z-index: 3;
            background: transparent !important;
            /* Remove blue background by using screen blend mode */
            mix-blend-mode: screen;
        }
        
        /* Remove any blue background from logo container */
        .netflix-logo {
            background: transparent !important;
        }
        
        /* Alternative approach: if screen doesn't work well, use lighten */
        @supports (mix-blend-mode: lighten) {
            .netflix-logo img {
                mix-blend-mode: lighten;
            }
        }
        
        /* Concentric circles around logo */
        .logo-circle {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border-radius: 50%;
            border: 2px solid rgba(0, 255, 204, 0.6);
            opacity: 0;
            z-index: 1;
            pointer-events: none;
        }
        
        .logo-circle::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100%;
            height: 100%;
            border-radius: 50%;
            box-shadow: 
                0 0 20px rgba(0, 255, 204, 0.4),
                0 0 40px rgba(0, 255, 204, 0.3),
                inset 0 0 20px rgba(0, 255, 204, 0.2);
        }
        
        .logo-circle:nth-child(1) {
            width: 240px;
            height: 240px;
            animation: circleExpand1 2s ease-out 1.8s forwards;
            border-color: rgba(0, 255, 204, 0.8);
        }
        
        .logo-circle:nth-child(2) {
            width: 280px;
            height: 280px;
            animation: circleExpand2 2s ease-out 1.9s forwards;
            border-color: rgba(0, 255, 204, 0.6);
        }
        
        .logo-circle:nth-child(3) {
            width: 320px;
            height: 320px;
            animation: circleExpand3 2s ease-out 2s forwards;
            border-color: rgba(0, 255, 204, 0.4);
        }
        
        .logo-circle:nth-child(4) {
            width: 360px;
            height: 360px;
            animation: circleExpand4 2s ease-out 2.1s forwards;
            border-color: rgba(0, 255, 204, 0.3);
        }
        
        @keyframes circleExpand1 {
            0% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0.3);
            }
            30% {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1.1);
            }
            70% {
                opacity: 0.8;
                transform: translate(-50%, -50%) scale(1);
            }
            100% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(1.2);
            }
        }
        
        @keyframes circleExpand2 {
            0% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0.3);
            }
            35% {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1.05);
            }
            70% {
                opacity: 0.6;
                transform: translate(-50%, -50%) scale(1);
            }
            100% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(1.15);
            }
        }
        
        @keyframes circleExpand3 {
            0% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0.3);
            }
            40% {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1.05);
            }
            70% {
                opacity: 0.4;
                transform: translate(-50%, -50%) scale(1);
            }
            100% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(1.1);
            }
        }
        
        @keyframes circleExpand4 {
            0% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0.3);
            }
            45% {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1.03);
            }
            70% {
                opacity: 0.3;
                transform: translate(-50%, -50%) scale(1);
            }
            100% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(1.08);
            }
        }
        
        /* Pulsing inner glow around logo */
        .logo-glow-ring {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 220px;
            height: 220px;
            border-radius: 50%;
            border: 3px solid transparent;
            background: radial-gradient(circle, rgba(0, 255, 204, 0.3) 0%, transparent 70%);
            opacity: 0;
            z-index: 2;
            pointer-events: none;
            animation: glowPulse 2s ease-out 1.8s forwards;
        }
        
        @keyframes glowPulse {
            0% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0.8);
            }
            30% {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1.05);
            }
            60% {
                opacity: 0.7;
                transform: translate(-50%, -50%) scale(1);
            }
            100% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(1.1);
            }
        }
        
        @keyframes netflixEntrance {
            0% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0.2) rotate(-10deg);
            }
            20% {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1.15) rotate(2deg);
            }
            40% {
                transform: translate(-50%, -50%) scale(0.95) rotate(-1deg);
            }
            60% {
                transform: translate(-50%, -50%) scale(1.05) rotate(0deg);
            }
            80% {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1) rotate(0deg);
            }
            100% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0.9) rotate(0deg);
            }
        }
        
        @keyframes logoGlow {
            0% {
                filter: drop-shadow(0 0 10px rgba(0, 255, 204, 0.3));
            }
            30% {
                filter: drop-shadow(0 0 50px rgba(0, 255, 204, 1)) 
                        drop-shadow(0 0 80px rgba(0, 204, 255, 0.8))
                        drop-shadow(0 0 100px rgba(244, 114, 182, 0.4));
            }
            60% {
                filter: drop-shadow(0 0 50px rgba(0, 255, 204, 1)) 
                        drop-shadow(0 0 80px rgba(0, 204, 255, 0.8))
                        drop-shadow(0 0 100px rgba(244, 114, 182, 0.4));
            }
            100% {
                filter: drop-shadow(0 0 20px rgba(0, 255, 204, 0.3));
            }
        }
        
        /* Glowing dots that spiral in */
        .spiral-dot {
            position: absolute;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #00ffcc;
            box-shadow: 0 0 10px rgba(0, 255, 204, 0.8);
            top: 50%;
            left: 50%;
            animation: spiralIn 1.5s ease-out forwards;
        }
        
        @keyframes spiralIn {
            0% {
                opacity: 0;
                transform: translate(var(--start-x), var(--start-y)) scale(0);
            }
            50% {
                opacity: 1;
            }
            100% {
                opacity: 0;
                transform: translate(0, 0) scale(1.5);
            }
        }
        
        /* Responsive adjustments for Netflix animation */
        @media (max-width: 768px) {
            .netflix-logo {
                width: 150px;
                height: 150px;
            }
            .netflix-logo-text {
                font-size: 28px;
                letter-spacing: 3px;
                margin-top: 15px;
            }
            .logo-circle:nth-child(1) { width: 180px; height: 180px; }
            .logo-circle:nth-child(2) { width: 210px; height: 210px; }
            .logo-circle:nth-child(3) { width: 240px; height: 240px; }
            .logo-circle:nth-child(4) { width: 270px; height: 270px; }
            .logo-glow-ring { width: 165px; height: 165px; }
            .energy-wave:nth-child(1) { width: 150px; height: 150px; }
            .energy-wave:nth-child(2) { width: 220px; height: 220px; }
            .energy-wave:nth-child(3) { width: 300px; height: 300px; }
        }
        
        @media (max-width: 480px) {
            .netflix-logo {
                width: 120px;
                height: 120px;
            }
            .netflix-logo-text {
                font-size: 22px;
                letter-spacing: 2px;
                margin-top: 12px;
            }
            .logo-circle:nth-child(1) { width: 144px; height: 144px; }
            .logo-circle:nth-child(2) { width: 168px; height: 168px; }
            .logo-circle:nth-child(3) { width: 192px; height: 192px; }
            .logo-circle:nth-child(4) { width: 216px; height: 216px; }
            .logo-glow-ring { width: 132px; height: 132px; }
            .energy-wave:nth-child(1) { width: 120px; height: 120px; }
            .energy-wave:nth-child(2) { width: 180px; height: 180px; }
            .energy-wave:nth-child(3) { width: 250px; height: 250px; }
        }
        
        /* Particle/Star Effect for Front-end */
        .content-particles {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            overflow: hidden;
            pointer-events: none;
            z-index: 0;
        }
        
        .content-particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: linear-gradient(135deg, #00ffcc, #00ccff);
            border-radius: 50%;
            opacity: 0;
            animation: contentFloatParticle 20s infinite linear;
            box-shadow: 0 0 6px rgba(0, 255, 204, 0.8);
        }
        
        @keyframes contentFloatParticle {
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
    </style>
    
    <!-- Create and start audio EARLY in head - before body -->
    <script>
        // Create audio element in head, before body loads
        (function() {
            const hasSeenAnimation = typeof sessionStorage !== 'undefined' ? sessionStorage.getItem('netflixAnimationShown') : null;
            
            if (!hasSeenAnimation && document.body === null) {
                // Create audio element immediately in head
                const audio = document.createElement('audio');
                audio.id = 'netflix-intro-sound';
                audio.preload = 'auto';
                audio.volume = 0.6;
                
                // Add sources (try multiple paths - Netflix Intro.mp3 is the actual file)
                // Encode space as %20 for URL
                const sources = [
                    'public/assets/audio/Netflix%20Intro.mp3',
                    'public/assets/audio/Netflix Intro.mp3',
                    'public/assets/audio/Netflix.Intro.mp3',
                    'public/assets/audio/netflix.intro.mp3'
                ];
                
                sources.forEach(function(src) {
                    const source = document.createElement('source');
                    source.src = src;
                    source.type = 'audio/mpeg';
                    audio.appendChild(source);
                });
                
                // Store in window for later access
                window._netflixAudio = audio;
                
                // When body exists, append audio
                function attachAudio() {
                    if (document.body && !document.getElementById('netflix-intro-sound')) {
                        document.body.appendChild(audio);
                        
                        // Try to start playing immediately
                        function startPlay() {
                            if (audio.readyState >= 2) {
                                audio.currentTime = 0;
                                audio.play().catch(function() {
                                    // If fails, try with canplay
                                    audio.addEventListener('canplay', function() {
                                        audio.play().catch(function() {});
                                    }, { once: true });
                                });
                            } else {
                                audio.load();
                                audio.addEventListener('canplay', function() {
                                    audio.currentTime = 0;
                                    audio.play().catch(function() {});
                                }, { once: true });
                            }
                        }
                        
                        // Try immediately
                        startPlay();
                        
                        // Also try after a tiny delay
                        setTimeout(startPlay, 5);
                    }
                }
                
                // Try to attach when body is ready
                if (document.body) {
                    attachAudio();
                } else {
                    var observer = new MutationObserver(function() {
                        if (document.body) {
                            attachAudio();
                            observer.disconnect();
                        }
                    });
                    observer.observe(document.documentElement, { childList: true });
                }
            }
        })();
    </script>
</head>
<body>
    <!-- Page Transition Loader (for navigation between pages) -->
    <div id="page-transition-loader" class="page-transition-loader">
        <div class="transition-spinner"></div>
    </div>
    
    <!-- Netflix-style Loading Screen (Intro Animation - First Load Only) -->
    <div id="page-loader" class="page-loader">
        <!-- Netflix intro sound (backup if not created in head) -->
        <audio id="netflix-intro-sound" preload="auto">
            <source src="public/assets/audio/Netflix%20Intro.mp3" type="audio/mpeg">
            <source src="public/assets/audio/Netflix Intro.mp3" type="audio/mpeg">
            <source src="public/assets/audio/Netflix.Intro.mp3" type="audio/mpeg">
            <source src="public/assets/audio/netflix.intro.mp3" type="audio/mpeg">
        </audio>
        
        <script>
            // Execute IMMEDIATELY when body loads - AGGRESSIVE audio start
            (function() {
                const hasSeenAnimation = typeof sessionStorage !== 'undefined' ? sessionStorage.getItem('netflixAnimationShown') : null;
                
                if (!hasSeenAnimation) {
                    // Use audio from head if available, otherwise get from body
                    let audio = window._netflixAudio || document.getElementById('netflix-intro-sound');
                    
                    if (!audio) {
                        // If still no audio, wait a tiny bit and try again
                        setTimeout(function() {
                            audio = document.getElementById('netflix-intro-sound');
                            if (audio) startAudioPlayback(audio);
                        }, 1);
                    } else {
                        startAudioPlayback(audio);
                    }
                    
                    function startAudioPlayback(audioElement) {
                        if (!audioElement) return;
                        
                        // Debug logging
                        console.log('Starting Netflix audio playback');
                        console.log('Audio element:', audioElement);
                        console.log('Current src:', audioElement.currentSrc);
                        console.log('Sources:', audioElement.querySelectorAll('source'));
                        
                        // Error handling
                        audioElement.addEventListener('error', function(e) {
                            console.error('Netflix audio error:', audioElement.error);
                            console.error('Error code:', audioElement.error ? audioElement.error.code : 'unknown');
                            console.error('Tried src:', audioElement.currentSrc);
                        }, { once: true });
                        
                        audioElement.addEventListener('loadeddata', function() {
                            console.log('Netflix audio loaded:', audioElement.currentSrc);
                        }, { once: true });
                        
                        // Set properties
                        audioElement.volume = 0.6;
                        audioElement.currentTime = 0;
                        
                        // Force load
                        try {
                            audioElement.load();
                        } catch(e) {
                            console.error('Error in audio.load():', e);
                        }
                        
                        let hasStarted = false;
                        let attempts = 0;
                        const maxAttempts = 100; // Try for ~1 second (10ms * 100)
                        
                        // Aggressive polling to start playback ASAP
                        function aggressivePlay() {
                            attempts++;
                            
                            if (hasStarted) {
                                return; // Already playing
                            }
                            
                            if (attempts > maxAttempts) {
                                return; // Give up after max attempts
                            }
                            
                            // Try to play if we have any data
                            if (audioElement.readyState >= 1) {
                                audioElement.currentTime = 0;
                                const playPromise = audioElement.play();
                                
                                if (playPromise !== undefined) {
                                    playPromise.then(function() {
                                        hasStarted = true;
                                    }).catch(function() {
                                        // Failed, will retry
                                    });
                                }
                                
                                // If successfully playing, stop polling
                                if (!audioElement.paused) {
                                    hasStarted = true;
                                    return;
                                }
                            }
                            
                            // Continue polling
                            setTimeout(aggressivePlay, 10);
                        }
                        
                        // Start aggressive polling immediately
                        aggressivePlay();
                        
                        // Also try event-based approach
                        function tryPlayOnEvent() {
                            if (!hasStarted) {
                                audioElement.currentTime = 0;
                                audioElement.play().then(function() {
                                    hasStarted = true;
                                }).catch(function() {});
                            }
                        }
                        
                        audioElement.addEventListener('loadedmetadata', tryPlayOnEvent, { once: true });
                        audioElement.addEventListener('loadeddata', tryPlayOnEvent, { once: true });
                        audioElement.addEventListener('canplay', tryPlayOnEvent, { once: true });
                        audioElement.addEventListener('canplaythrough', tryPlayOnEvent, { once: true });
                    }
                }
            })();
        </script>
        
        <div class="loader-content">
            <!-- Energy waves converging to center -->
            <div class="energy-wave"></div>
            <div class="energy-wave"></div>
            <div class="energy-wave"></div>
            
            <!-- Animated particles container -->
            <div class="animated-particles" id="animatedParticles"></div>
            
            <!-- Logo appears after particles -->
            <div class="netflix-logo">
                <!-- Concentric circles around logo -->
                <div class="logo-circle"></div>
                <div class="logo-circle"></div>
                <div class="logo-circle"></div>
                <div class="logo-circle"></div>
                
                <!-- Inner glow ring -->
                <div class="logo-glow-ring"></div>
                
                <!-- Logo image -->
                <img src="public/images/logo.png" alt="Game Master Logo">
                
                <!-- Game Master text -->
                <div class="netflix-logo-text">Game Master</div>
            </div>
        </div>
    </div>
    
    <?php
    // Determine if we're on an authentication page (for other purposes, but navbar stays visible)
    $action = $_GET['action'] ?? '';
    $controller = $_GET['controller'] ?? '';
    $isAuthPage = in_array($action, ['login', 'register', 'forgot_password', 'reset_password', 'verify_email', 'verify_email_page', 'resend_verification', 'face_login', 'face_registration']) || 
                  ($currentPage === 'login' || $currentPage === 'register');
    
    // Get user information (moved here to be available for both nav-links and user-actions)
    $username = null;
    $userAvatar = null;
    $userMedal = 'none';
    $avatarUrl = null;
    
    if (isset($_SESSION['user_id'])) {
        $username = $_SESSION['username'] ?? 'Utilisateur';
        $userAvatar = $_SESSION['avatar'] ?? null;
        
        // ALWAYS fetch medal fresh from database FIRST (ignore session cache)
        if (isset($db)) {
            try {
                $checkMedalQuery = "SHOW COLUMNS FROM users LIKE 'medal'";
                $checkMedalStmt = $db->prepare($checkMedalQuery);
                $checkMedalStmt->execute();
                $hasMedalColumn = $checkMedalStmt->rowCount() > 0;
                
                if ($hasMedalColumn) {
                    $medalQuery = "SELECT medal FROM users WHERE id = ?";
                    $medalStmt = $db->prepare($medalQuery);
                    $medalStmt->execute([$_SESSION['user_id']]);
                    $medalRow = $medalStmt->fetch(PDO::FETCH_ASSOC);
                    $userMedal = $medalRow['medal'] ?? 'none';
                    $_SESSION['medal'] = $userMedal;
                }
            } catch (Exception $e) {
                error_log("Error fetching medal in header: " . $e->getMessage());
                $userMedal = $_SESSION['medal'] ?? 'none';
            }
        }
        
        // If username is not set, try to get it from the database
        if (!isset($_SESSION['username']) && isset($db)) {
            require_once "models/User.php";
            $userModel = new User($db);
            $userModel->id = $_SESSION['user_id'];
            if ($userModel->readOne()) {
                $username = $userModel->username ?? 'Utilisateur';
                $userAvatar = $userModel->avatar ?? null;
                $_SESSION['username'] = $username;
                $_SESSION['avatar'] = $userAvatar;
            }
        }
        
        // Clean avatar path
        if (!empty($userAvatar)) {
            $avatarPath = trim($userAvatar);
            $avatarPath = ltrim($avatarPath, '/');
            $avatarPath = str_replace('projet01/', '', $avatarPath);
            
            if (strpos($avatarPath, 'public/') === 0) {
                $avatarUrl = $avatarPath;
            } elseif (strpos($avatarPath, 'uploads/') === 0 || strpos($avatarPath, 'assets/') === 0) {
                $avatarUrl = 'public/' . $avatarPath;
            } else {
                $avatarUrl = 'public/' . ltrim($avatarPath, '/');
            }
        }
    }
    ?>
    
    <!-- Navigation -->
    <nav id="navbar">
        <div class="nav-container">
            <!-- Logo Block - Left -->
            <div class="logo-block">
                <a href="?controller=formation&action=userDashboard" class="logo">
                    <div class="logo-icon">
                        <img src="public/images/logo.png" alt="Game Master Logo" class="logo-image" onerror="this.style.display='none'; this.parentElement.querySelector('.logo-fallback').style.display='block';">
                        <svg viewBox="0 0 24 24" class="logo-fallback" style="display: none;">
                            <path d="M3 13h2v8H3zm4-8h2v13H7zm4-2h2v15h-2zm4 4h2v11h-2zm4-2h2v13h-2z" fill="currentColor"/>
                        </svg>
                    </div>
                    <span class="logo-text">
                        <span class="prism">Game</span>
                        <span class="flux">Master</span>
                    </span>
                </a>
            </div>
            
            <!-- Navigation Links Block - Center -->
            <ul class="nav-links">
                <li><a href="?controller=formation&action=userDashboard" class="<?php echo (isset($currentPage) && $currentPage == 'dashboard') ? 'active' : ''; ?>">Accueil</a></li>
                
                <!-- Formations/Éducations Dropdown -->
                <li>
                    <?php 
                    $formationEducationLabel = "📚 Formations/Éducations";
                    if (isset($currentPage)) {
                        if ($currentPage == 'formations') $formationEducationLabel = "📖 Formations";
                        elseif ($currentPage == 'educations') $formationEducationLabel = "📝 Éducations";
                    }
                    ?>
                    <select id="formationEducationSelect" class="nav-select" onchange="if(this.value) window.location.href=this.value;">
                        <option value="" selected><?php echo $formationEducationLabel; ?></option>
                        <option value="?controller=formation&action=list" <?php echo (isset($currentPage) && $currentPage == 'formations') ? 'selected' : ''; ?>>📖 Formations</option>
                        <option value="?controller=education&action=list" <?php echo (isset($currentPage) && $currentPage == 'educations') ? 'selected' : ''; ?>>📝 Éducations</option>
                    </select>
                </li>
                
                <li><a href="?action=games" class="<?php echo (isset($currentPage) && $currentPage == 'games') ? 'active' : ''; ?>">Jeux</a></li>
                
                <!-- Projets/Donations Dropdown -->
                <li>
                    <?php 
                    $projectDonationLabel = "🌍 Projets/Donations";
                    if (isset($currentPage)) {
                        if ($currentPage == 'projects') $projectDonationLabel = "🌍 Projets";
                        elseif ($currentPage == 'donation') $projectDonationLabel = "💝 Donations";
                    }
                    ?>
                    <select id="projectDonationSelect" class="nav-select" onchange="if(this.value) window.location.href=this.value;">
                        <option value="" selected><?php echo $projectDonationLabel; ?></option>
                        <option value="?action=projects" <?php echo (isset($currentPage) && $currentPage == 'projects') ? 'selected' : ''; ?>>🌍 Projets</option>
                        <option value="?action=donation" <?php echo (isset($currentPage) && $currentPage == 'donation') ? 'selected' : ''; ?>>💝 Donations</option>
                    </select>
                </li>
                
                <!-- Événements/Participations Dropdown -->
                <li>
                    <?php 
                    $isEventsPage = isset($currentPage) && $currentPage == 'events';
                    $isParticipationsPage = isset($currentPage) && $currentPage == 'participations';
                    $eventParticipationLabel = "📅 Événements/Mes Participations";
                    if ($isEventsPage) {
                        $eventParticipationLabel = "📅 Événements";
                    } elseif ($isParticipationsPage) {
                        $eventParticipationLabel = "🎫 Mes Participations";
                    }
                    ?>
                    <select id="eventParticipationSelect" class="nav-select" onchange="if(this.value) window.location.href=this.value;">
                        <option value="" disabled <?php echo (!$isEventsPage && !$isParticipationsPage) ? 'selected' : ''; ?>><?php echo htmlspecialchars($eventParticipationLabel); ?></option>
                        <option value="?action=events" <?php echo $isEventsPage ? 'selected' : ''; ?>>📅 Événements</option>
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <option value="?action=my_participations" <?php echo $isParticipationsPage ? 'selected' : ''; ?>>🎫 Mes Participations</option>
                        <?php endif; ?>
                    </select>
                </li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="?controller=test&action=status" class="<?php echo (isset($currentPage) && $currentPage == 'test') ? 'active' : ''; ?>">📝 Test QCM</a></li>
                    <li><a href="?controller=gameLibrary&action=list" class="<?php echo (isset($currentPage) && $currentPage == 'games_library') ? 'active' : ''; ?>">🎮 Bibliothèque de Jeux</a></li>
                    <li><a href="?action=mes_reclamations" class="<?php echo (isset($currentPage) && ($currentPage == 'mes_reclamations' || $currentPage == 'reclamation_create' || $currentPage == 'reclamation_edit')) ? 'active' : ''; ?>">📋 Mes Réclamations</a></li>
                <?php endif; ?>
            </ul>
            
            <!-- User Actions Block - Right -->
            <ul class="user-actions">
                <?php if(isset($_SESSION['user_id'])): ?>
                <li style="display: flex; align-items: center; gap: 10px; color: #00ffcc; font-weight: 600; padding: 10px 16px; border-radius: 8px; background: rgba(0, 255, 204, 0.1); border: 1px solid rgba(0, 255, 204, 0.3); white-space: nowrap; height: 40px; line-height: 1;">
                    <a href="?action=profile" style="text-decoration: none; display: flex; align-items: center; gap: 10px;">
                        <?php if (!empty($avatarUrl)): ?>
                            <img src="<?php echo htmlspecialchars($avatarUrl); ?>" 
                                 alt="Avatar" 
                                 style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 2px solid #00ffcc; cursor: pointer; transition: all 0.3s ease;"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                 onmouseover="this.style.transform='scale(1.1)'; this.style.boxShadow='0 0 15px rgba(0, 255, 204, 0.5)';"
                                 onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none';">
                            <div style="display: none; width: 32px; height: 32px; border-radius: 50%; background: <?php echo '#' . substr(md5($username), 0, 6); ?>; align-items: center; justify-content: center; border: 2px solid #00ffcc; cursor: pointer; transition: all 0.3s ease;"
                                 onmouseover="this.style.transform='scale(1.1)'; this.style.boxShadow='0 0 15px rgba(0, 255, 204, 0.5)';"
                                 onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none';">
                                <span style="color: #ffffff; font-size: 18px; font-weight: bold;"><?php echo htmlspecialchars(strtoupper(substr($username, 0, 1))); ?></span>
                            </div>
                        <?php else: 
                            $firstLetter = strtoupper(substr($username, 0, 1));
                            $color = '#' . substr(md5($username), 0, 6);
                        ?>
                            <div style="width: 32px; height: 32px; border-radius: 50%; background: <?php echo $color; ?>; display: flex; align-items: center; justify-content: center; border: 2px solid #00ffcc; cursor: pointer; transition: all 0.3s ease;"
                                 onmouseover="this.style.transform='scale(1.1)'; this.style.boxShadow='0 0 15px rgba(0, 255, 204, 0.5)';"
                                 onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none';">
                                <span style="color: #ffffff; font-size: 18px; font-weight: bold;"><?php echo htmlspecialchars($firstLetter); ?></span>
                            </div>
                        <?php endif; ?>
                        <span style="display: flex; align-items: center; gap: 8px; color: #00ffcc;">
                            <?php echo htmlspecialchars($username); ?>
                            <?php 
                            $medal = $userMedal ?? 'none';
                            if ($medal !== 'none'): 
                                $medalIcons = [
                                    'bronze' => '🥉',
                                    'silver' => '🥈',
                                    'gold' => '🥇'
                                ];
                                $medalNames = [
                                    'bronze' => 'Bronze',
                                    'silver' => 'Argent',
                                    'gold' => 'Or'
                                ];
                                $medalShadows = [
                                    'bronze' => '0 0 10px rgba(205, 127, 50, 0.6)',
                                    'silver' => '0 0 10px rgba(192, 192, 192, 0.6)',
                                    'gold' => '0 0 15px rgba(255, 215, 0, 0.8)'
                                ];
                            ?>
                                <span style="font-size: 22px; 
                                             display: inline-block; 
                                             filter: drop-shadow(<?php echo $medalShadows[$medal]; ?>);
                                             animation: medalGlow 2s ease-in-out infinite alternate;
                                             cursor: pointer;
                                             transition: transform 0.3s ease;" 
                                      title="Médaille <?php echo $medalNames[$medal]; ?>"
                                      onmouseover="this.style.transform='scale(1.15)';"
                                      onmouseout="this.style.transform='scale(1)';">
                                    <?php echo $medalIcons[$medal]; ?>
                                </span>
                            <?php endif; ?>
                        </span>
                    </a>
                </li>
                    <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <li><a href="?controller=formation&action=dashboard" class="admin-btn">Administration</a></li>
                    <?php endif; ?>
                <li><a href="?action=logout" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?');">Déconnexion</a></li>
                <?php else: ?>
                    <li><a href="?action=login">Connexion</a></li>
                    <li><a href="?action=register" class="cta-button">Inscription</a></li>
                <?php endif; ?>
            </ul>
            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
        <ul class="nav-links-mobile" id="navLinksMobile">
            <?php if(isset($_SESSION['user_id'])): ?>
            <!-- Avatar cliquable pour mobile -->
            <li style="padding: 15px; border-bottom: 1px solid rgba(0, 255, 204, 0.2); margin-bottom: 10px;">
                <a href="?action=profile" style="display: flex; align-items: center; gap: 12px; text-decoration: none; color: #00ffcc;">
                    <?php if (!empty($avatarUrl)): ?>
                        <img src="<?php echo htmlspecialchars($avatarUrl); ?>" 
                             alt="Avatar" 
                             style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #00ffcc; cursor: pointer; transition: all 0.3s ease;"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                             onmouseover="this.style.transform='scale(1.1)'; this.style.boxShadow='0 0 15px rgba(0, 255, 204, 0.5)';"
                             onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none';">
                        <div style="display: none; width: 40px; height: 40px; border-radius: 50%; background: <?php echo '#' . substr(md5($username), 0, 6); ?>; align-items: center; justify-content: center; border: 2px solid #00ffcc; cursor: pointer; transition: all 0.3s ease;"
                             onmouseover="this.style.transform='scale(1.1)'; this.style.boxShadow='0 0 15px rgba(0, 255, 204, 0.5)';"
                             onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none';">
                            <span style="color: #ffffff; font-size: 20px; font-weight: bold;"><?php echo htmlspecialchars(strtoupper(substr($username, 0, 1))); ?></span>
                        </div>
                    <?php else: 
                        $firstLetter = strtoupper(substr($username, 0, 1));
                        $color = '#' . substr(md5($username), 0, 6);
                    ?>
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: <?php echo $color; ?>; display: flex; align-items: center; justify-content: center; border: 2px solid #00ffcc; cursor: pointer; transition: all 0.3s ease;"
                             onmouseover="this.style.transform='scale(1.1)'; this.style.boxShadow='0 0 15px rgba(0, 255, 204, 0.5)';"
                             onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none';">
                            <span style="color: #ffffff; font-size: 20px; font-weight: bold;"><?php echo htmlspecialchars($firstLetter); ?></span>
                        </div>
                    <?php endif; ?>
                    <div>
                        <div style="font-weight: 600; font-size: 16px;"><?php echo htmlspecialchars($username); ?></div>
                        <div style="font-size: 12px; color: rgba(255, 255, 255, 0.6);">Voir mon profil</div>
                    </div>
                </a>
            </li>
            <?php endif; ?>
            
            <li><a href="?controller=formation&action=userDashboard">Accueil</a></li>
            
            <!-- Formations/Éducations Dropdown Mobile -->
            <li>
                <select id="formationEducationSelectMobile" class="nav-select" onchange="if(this.value) window.location.href=this.value;" style="width: 100%; background: rgba(0, 255, 204, 0.1); border: 1px solid rgba(0, 255, 204, 0.3); color: #00ffcc; padding: 8px 12px; border-radius: 8px; cursor: pointer; font-size: 14px; outline: none; margin: 5px 0;">
                    <option value="" selected disabled>📚 Formations/Éducations</option>
                    <option value="?controller=formation&action=list">📖 Formations</option>
                    <option value="?controller=education&action=list">📝 Éducations</option>
                </select>
            </li>
            
            <li><a href="?action=games">Jeux</a></li>
            
            <!-- Projets/Donations Dropdown Mobile -->
            <li>
                <select id="projectDonationSelectMobile" class="nav-select" onchange="if(this.value) window.location.href=this.value;" style="width: 100%; background: rgba(0, 255, 204, 0.1); border: 1px solid rgba(0, 255, 204, 0.3); color: #00ffcc; padding: 8px 12px; border-radius: 8px; cursor: pointer; font-size: 14px; outline: none; margin: 5px 0;">
                    <option value="" selected disabled>🌍 Projets/Donations</option>
                    <option value="?action=projects">🌍 Projets</option>
                    <option value="?action=donation">💝 Donations</option>
                </select>
            </li>
            
            <!-- Événements/Participations Dropdown Mobile -->
            <li>
                <?php 
                $isEventsPage = isset($currentPage) && $currentPage == 'events';
                $isParticipationsPage = isset($currentPage) && $currentPage == 'participations';
                $eventParticipationLabelMobile = "📅 Événements/Mes Participations";
                if ($isEventsPage) {
                    $eventParticipationLabelMobile = "📅 Événements";
                } elseif ($isParticipationsPage) {
                    $eventParticipationLabelMobile = "🎫 Mes Participations";
                }
                ?>
                <select id="eventParticipationSelectMobile" class="nav-select" onchange="if(this.value) window.location.href=this.value;" style="width: 100%; background: rgba(0, 255, 204, 0.1); border: 1px solid rgba(0, 255, 204, 0.3); color: #00ffcc; padding: 8px 12px; border-radius: 8px; cursor: pointer; font-size: 14px; outline: none; margin: 5px 0;">
                    <option value="" disabled <?php echo (!$isEventsPage && !$isParticipationsPage) ? 'selected' : ''; ?>><?php echo htmlspecialchars($eventParticipationLabelMobile); ?></option>
                    <option value="?action=events" <?php echo $isEventsPage ? 'selected' : ''; ?>>📅 Événements</option>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <option value="?action=my_participations" <?php echo $isParticipationsPage ? 'selected' : ''; ?>>🎫 Mes Participations</option>
                    <?php endif; ?>
                </select>
            </li>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <li><a href="?controller=test&action=status">📝 Test QCM</a></li>
                <li><a href="?controller=gameLibrary&action=list">🎮 Bibliothèque de Jeux</a></li>
                <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li><a href="?controller=formation&action=dashboard" style="background: linear-gradient(135deg, #9333ea, #7c3aed); padding: 8px 20px; border-radius: 8px; color: white !important; text-decoration: none; font-weight: 600;">Administration</a></li>
                <?php endif; ?>
                <li><a href="?action=logout" style="color: #ff6b6b; font-weight: 600;" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?');">Déconnexion</a></li>
            <?php else: ?>
                <li><a href="?action=login">Connexion</a></li>
                <li><a href="?action=register">Inscription</a></li>
            <?php endif; ?>
        </ul>
    </nav>
