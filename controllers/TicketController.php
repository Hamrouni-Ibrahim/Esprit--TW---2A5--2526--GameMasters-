<?php
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/User.php';

class TicketController {
    private $db;
    private $eventModel;

    public function __construct($db) {
        $this->db = $db;
        $this->eventModel = new Event($db);
    }

    public function generateTicket($participation_id) {
        // Get participation details
        $participation = $this->eventModel->getParticipationById($participation_id);
        
        if (!$participation) {
            die("Participation non trouvée.");
        }
        
        // Get user role if logged in
        $userRole = 'Utilisateur';
        if (isset($_SESSION['email']) && $participation['email'] === $_SESSION['email']) {
            $userRole = $_SESSION['role'] ?? 'Utilisateur';
        }
        
        // Check if TCPDF is available (check multiple possible locations)
        $tcpdfPaths = [
            __DIR__ . '/../vendor/tecnickcom/tcpdf/tcpdf.php',
            __DIR__ . '/../../vendor/tecnickcom/tcpdf/tcpdf.php'
        ];
        
        $tcpdfPath = null;
        foreach ($tcpdfPaths as $path) {
            if (file_exists($path)) {
                $tcpdfPath = $path;
                break;
            }
        }
        
        if ($tcpdfPath) {
            require_once $tcpdfPath;
            $this->generateTicketWithTCPDF($participation, $userRole);
        } else {
            // Fallback: Generate simple HTML ticket
            $this->generateTicketHTML($participation, $userRole);
        }
    }

    private function generateTicketWithTCPDF($participation, $userRole) {
        // Clean output buffer
        if (ob_get_length()) ob_end_clean();
        
        // Create PDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Add a page
        $pdf->AddPage();
        
        // Set font
        $pdf->SetFont('helvetica', 'B', 20);
        
        // Title
        $pdf->Cell(0, 15, 'TICKET D\'ACCES', 0, 1, 'C');
        $pdf->Ln(5);
        
        // Event name
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, htmlspecialchars($participation['nom_evenet']), 0, 1, 'C');
        $pdf->Ln(5);
        
        // Information section
        $pdf->SetFont('helvetica', '', 12);
        
        // User information
        $pdf->Cell(0, 8, 'Nom: ' . htmlspecialchars($participation['nom']), 0, 1, 'L');
        $pdf->Cell(0, 8, 'Email: ' . htmlspecialchars($participation['email']), 0, 1, 'L');
        $pdf->Cell(0, 8, 'Role: ' . htmlspecialchars($userRole), 0, 1, 'L');
        $pdf->Ln(5);
        
        // Event dates
        if (isset($participation['date_debut']) && isset($participation['date_fin'])) {
            $date_debut = new DateTime($participation['date_debut']);
            $date_fin = new DateTime($participation['date_fin']);
            $pdf->Cell(0, 8, 'Date de debut: ' . $date_debut->format('d/m/Y H:i'), 0, 1, 'L');
            $pdf->Cell(0, 8, 'Date de fin: ' . $date_fin->format('d/m/Y H:i'), 0, 1, 'L');
        }
        $pdf->Ln(10);
        
        // QR Code (imaginary - we'll create a simple placeholder)
        $qrData = 'EVENT:' . $participation['idevent'] . '|PART:' . $participation['id'] . '|EMAIL:' . $participation['email'];
        
        // Generate QR code
        $style = array(
            'border' => 2,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false,
            'module_width' => 1,
            'module_height' => 1
        );
        
        $pdf->write2DBarcode($qrData, 'QRCODE,L', 80, 120, 50, 50, $style, 'N');
        
        // Add text below QR code
        $pdf->SetY(175);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 8, 'Code QR de verification', 0, 1, 'C');
        
        // Participation date
        $pdf->SetFont('helvetica', 'I', 10);
        $participation_date = new DateTime($participation['date_participation']);
        $pdf->Cell(0, 8, 'Date d\'inscription: ' . $participation_date->format('d/m/Y H:i'), 0, 1, 'C');
        
        // Output PDF
        $filename = 'Ticket_' . $participation['nom_evenet'] . '_' . date('Ymd') . '.pdf';
        $pdf->Output($filename, 'D'); // D = download
        exit;
    }

    private function generateTicketHTML($participation, $userRole) {
        // Generate simple HTML ticket as fallback
        header('Content-Type: text/html; charset=utf-8');
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Ticket d'Accès</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; max-width: 600px; margin: 0 auto; }
                .ticket { border: 2px solid #333; padding: 20px; border-radius: 10px; }
                .title { text-align: center; font-size: 24px; font-weight: bold; margin-bottom: 20px; }
                .info { margin: 10px 0; }
                .qr-placeholder { width: 150px; height: 150px; border: 2px dashed #333; margin: 20px auto; display: flex; align-items: center; justify-content: center; }
            </style>
        </head>
        <body>
            <div class="ticket">
                <div class="title">TICKET D'ACCES</div>
                <div style="text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 20px;">
                    <?= htmlspecialchars($participation['nom_evenet']) ?>
                </div>
                <div class="info"><strong>Nom:</strong> <?= htmlspecialchars($participation['nom']) ?></div>
                <div class="info"><strong>Email:</strong> <?= htmlspecialchars($participation['email']) ?></div>
                <div class="info"><strong>Role:</strong> <?= htmlspecialchars($userRole) ?></div>
                <?php if (isset($participation['date_debut']) && isset($participation['date_fin'])): 
                    $date_debut = new DateTime($participation['date_debut']);
                    $date_fin = new DateTime($participation['date_fin']);
                ?>
                    <div class="info"><strong>Date de début:</strong> <?= $date_debut->format('d/m/Y H:i') ?></div>
                    <div class="info"><strong>Date de fin:</strong> <?= $date_fin->format('d/m/Y H:i') ?></div>
                <?php endif; ?>
                <div class="qr-placeholder">
                    <div style="text-align: center;">QR CODE<br>(Imaginaire)</div>
                </div>
                <div style="text-align: center; font-style: italic; margin-top: 10px;">
                    Date d'inscription: <?= (new DateTime($participation['date_participation']))->format('d/m/Y H:i') ?>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

