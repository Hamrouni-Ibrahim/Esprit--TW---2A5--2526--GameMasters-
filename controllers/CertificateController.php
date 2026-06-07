<?php
require_once "models/TestAttempt.php";
require_once "models/TestApproval.php";
require_once "models/Medal.php";
require_once "models/User.php";

class CertificateController {
    
    // Generate and download certificate PDF
    public function downloadCertificate() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?action=login");
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $attemptId = $_GET['attempt_id'] ?? null;
        
        if (!$attemptId) {
            $_SESSION['error_message'] = "Tentative de test invalide.";
            header("Location: ?controller=test&action=status");
            exit;
        }
        
        // Get test attempt
        $testAttempt = new TestAttempt();
        $attempt = $testAttempt->getById($attemptId);
        
        if (!$attempt || $attempt['user_id'] != $userId) {
            $_SESSION['error_message'] = "Tentative de test non trouvée.";
            header("Location: ?controller=test&action=status");
            exit;
        }
        
        // Get approval
        require_once "models/TestApproval.php";
        $testApproval = new TestApproval();
        $approval = $testApproval->getByAttemptId($attemptId);
        
        // Check if test is approved
        if (!$approval || $approval['status'] !== 'approved') {
            $_SESSION['error_message'] = "Le certificat n'est disponible que pour les tests approuvés.";
            header("Location: ?controller=test&action=results&attempt_id=" . $attemptId);
            exit;
        }
        
        // Get user info
        require_once "config/database.php";
        $database = new Database();
        $conn = $database->getConnection();
        require_once "models/User.php";
        $userModel = new User($conn);
        $userModel->id = $userId;
        if (!$userModel->readOne()) {
            $_SESSION['error_message'] = "Utilisateur non trouvé.";
            header("Location: ?controller=test&action=status");
            exit;
        }
        
        // Ensure username is available - get from model or session as fallback
        $userName = $userModel->username ?? $_SESSION['username'] ?? 'Utilisateur';
        if (empty($userName)) {
            // Direct database query as last resort
            $userQuery = "SELECT username FROM users WHERE id = ?";
            $userStmt = $conn->prepare($userQuery);
            $userStmt->execute([$userId]);
            $userRow = $userStmt->fetch(PDO::FETCH_ASSOC);
            $userName = $userRow['username'] ?? 'Utilisateur';
        }
        
        // Get medal
        $medalModel = new Medal();
        $medal = $medalModel->getMedal($userId);
        
        if ($medal === 'none') {
            $_SESSION['error_message'] = "Aucune médaille attribuée. Le certificat n'est disponible que pour les utilisateurs ayant reçu une médaille.";
            header("Location: ?controller=test&action=results&attempt_id=" . $attemptId);
            exit;
        }
        
        // Load TCPDF
        $autoloadPaths = [
            __DIR__ . '/../vendor/autoload.php',
            __DIR__ . '/../vendor/autoload.php'
        ];
        
        $autoloadLoaded = false;
        foreach ($autoloadPaths as $path) {
            if (file_exists($path)) {
                require_once $path;
                $autoloadLoaded = true;
                break;
            }
        }
        
        if (!$autoloadLoaded) {
            die("Erreur: Bibliothèque TCPDF non trouvée.");
        }
        
        // Clean output buffer
        if (ob_get_length()) ob_end_clean();
        
        // Create PDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Set margins
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false, 0);
        
        // Add page (Landscape for certificate)
        $pdf->AddPage('L', 'A4');
        
        // Certificate dimensions
        $pageWidth = $pdf->getPageWidth(); // ~297mm for A4 landscape
        $pageHeight = $pdf->getPageHeight(); // ~210mm for A4 landscape
        
        // Clean color scheme
        $primaryColor = [25, 50, 100]; // Dark blue
        $accentColor = [184, 134, 11]; // Gold
        $textColor = [60, 60, 60]; // Dark gray
        
        // White background
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Rect(0, 0, $pageWidth, $pageHeight, 'F');
        
        // Subtle border
        $borderWidth = 4;
        $pdf->SetDrawColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->SetLineWidth($borderWidth);
        $pdf->Rect($borderWidth, $borderWidth, $pageWidth - ($borderWidth * 2), $pageHeight - ($borderWidth * 2), 'D');
        
        // ========== HEADER SECTION (Top: 40-120mm) ==========
        $currentY = 40;
        
        // Logo (if exists) - small, centered
        $logoPath = __DIR__ . '/../public/images/logo.png';
        if (file_exists($logoPath)) {
            $logoSize = 45;
            $logoX = ($pageWidth - $logoSize) / 2;
            $pdf->Image($logoPath, $logoX, $currentY, $logoSize, $logoSize, '', '', '', false, 300, '', false, false, 0);
            $currentY += $logoSize + 15;
        }
        
        // Main Title "CERTIFICAT"
        $pdf->SetFont('helvetica', 'B', 42);
        $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->SetXY(0, $currentY);
        $pdf->Cell($pageWidth, 20, 'CERTIFICAT', 0, 0, 'C');
        $currentY += 25;
        
        // Subtitle "DE RÉUSSITE"
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->SetTextColor($accentColor[0], $accentColor[1], $accentColor[2]);
        $pdf->SetXY(0, $currentY);
        $pdf->Cell($pageWidth, 12, 'DE RÉUSSITE', 0, 0, 'C');
        $currentY += 25;
        
        // ========== RECIPIENT SECTION (Middle: 120-150mm) ==========
        // Ensure username is not empty
        if (empty($userName) || $userName === 'Utilisateur') {
            $userName = $attempt['username'] ?? $_SESSION['username'] ?? 'Utilisateur';
        }
        $usernameText = strtoupper(trim($userName));
        
        // Username (large, centered, black)
        $pdf->SetFont('helvetica', 'B', 52);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY(0, $currentY);
        $pdf->Cell($pageWidth, 30, $usernameText, 0, 0, 'C');
        $currentY += 35;
        
        // Date under username
        $dateObj = new DateTime($attempt['submitted_at'] ?? $attempt['started_at']);
        $months = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
        $monthName = $months[(int)$dateObj->format('n') - 1];
        $date = $dateObj->format('d') . ' ' . $monthName . ' ' . $dateObj->format('Y');
        
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor($textColor[0], $textColor[1], $textColor[2]);
        $pdf->SetXY(0, $currentY);
        $pdf->Cell($pageWidth, 8, 'Délivré le ' . $date, 0, 0, 'C');
        $currentY += 18;
        
        // Decorative line under name and date
        $pdf->SetDrawColor($accentColor[0], $accentColor[1], $accentColor[2]);
        $pdf->SetLineWidth(1.5);
        $pdf->Line($pageWidth * 0.25, $currentY, $pageWidth * 0.75, $currentY);
        $currentY += 25;
        
        // ========== ACHIEVEMENT SECTION (150-170mm) ==========
        $medalNames = [
            'bronze' => 'Médaille de Bronze',
            'silver' => 'Médaille d\'Argent',
            'gold' => 'Médaille d\'Or'
        ];
        
        // Achievement text (simple, clear, centered)
        $pdf->SetFont('helvetica', '', 13);
        $pdf->SetTextColor($textColor[0], $textColor[1], $textColor[2]);
        $achievementText = "pour avoir obtenu la " . $medalNames[$medal] . "\navec un score de " . number_format($attempt['score'], 1) . "%";
        
        $pdf->SetXY($pageWidth * 0.15, $currentY);
        $pdf->MultiCell($pageWidth * 0.7, 8, $achievementText, 0, 'C', false, 1, '', '', true, 0, false, true, 0, 'M');
        
        // ========== FOOTER SECTION (Bottom: 170-210mm) ==========
        $footerY = $pageHeight - 70;
        
        // Signature line (above organization name)
        $pdf->SetDrawColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->SetLineWidth(0.5);
        $pdf->Line($pageWidth * 0.3, $footerY, $pageWidth * 0.7, $footerY);
        
        // Organization name
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->SetTextColor($primaryColor[0], $primaryColor[1], $primaryColor[2]);
        $pdf->SetXY(0, $footerY + 8);
        $pdf->Cell($pageWidth, 8, 'Game Masters Platform', 0, 0, 'C');
        
        // Certificate number (very small, at the very bottom)
        $certNumber = 'GM-' . str_pad($attemptId, 6, '0', STR_PAD_LEFT) . '-' . strtoupper(substr($medal, 0, 1)) . '-' . date('Y');
        $pdf->SetFont('helvetica', '', 7);
        $pdf->SetTextColor(140, 140, 140);
        $pdf->SetXY(0, $pageHeight - 15);
        $pdf->Cell($pageWidth, 5, 'Certificat N° ' . $certNumber, 0, 0, 'C');
        
        
        // Output PDF
        $filename = 'Certificat_' . $userName . '_' . ucfirst($medal) . '_' . date('Y-m-d') . '.pdf';
        $pdf->Output($filename, 'D');
        exit;
    }
}
?>

