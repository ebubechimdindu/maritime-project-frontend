<?php
session_start();
require_once('../backend/conn.php');
require_once('../vendor/autoload.php'); // For PDF generation, install using: composer require dompdf/dompdf

use Dompdf\Dompdf;
use Dompdf\Options;

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: adminLogin.php');
    exit();
}

$type = $_GET['type'] ?? '';
$filename = '';
$content = '';

switch($type) {
    case 'compliance':
        // Fetch compliance data
        $sql = "SELECT vc.*, v.vessel_name, v.imo_number, u.full_name as owner_name,
                (
                    safety_equipment_check + navigation_systems_check + 
                    crew_certification_check + environmental_compliance + 
                    hull_integrity_check + firefighting_equipment + 
                    medical_supplies_check + radio_equipment_check + 
                    waste_management_check + security_systems_check
                ) as compliance_score
                FROM vessel_compliance vc
                JOIN vessels v ON vc.vessel_id = v.vessel_id
                JOIN users u ON vc.user_id = u.user_id
                ORDER BY compliance_score DESC";
        $result = $conn->query($sql);
        
        $filename = 'compliance_report_' . date('Y-m-d') . '.pdf';
        $content = generateComplianceReport($result);
        break;

    case 'vessel':
        // Fetch vessel data
        $sql = "SELECT v.*, u.full_name as owner_name 
                FROM vessels v 
                JOIN users u ON v.user_id = u.user_id";
        $result = $conn->query($sql);
        
        $filename = 'vessel_status_report_' . date('Y-m-d') . '.pdf';
        $content = generateVesselReport($result);
        break;

    case 'inspection':
        // Fetch inspection data
        $sql = "SELECT v.*, u.full_name, vi.* 
                FROM vessels v 
                JOIN users u ON v.user_id = u.user_id 
                LEFT JOIN vessel_inspections vi ON v.vessel_id = vi.vessel_id 
                ORDER BY vi.inspection_date DESC";
        $result = $conn->query($sql);
        
        $filename = 'inspection_report_' . date('Y-m-d') . '.pdf';
        $content = generateInspectionReport($result);
        break;
}

// Generate PDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($content);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream($filename, array('Attachment' => true));

function generateComplianceReport($result) {
    $html = '
    <style>
        .report-header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; border: 1px solid #ddd; }
        th { background-color: #f5f5f5; }
        .compliance-score { font-weight: bold; }
    </style>
    <div class="report-header">
        <h1>Vessel Compliance Report</h1>
        <p>Generated on: ' . date('Y-m-d H:i:s') . '</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>Vessel Name</th>
                <th>IMO Number</th>
                <th>Owner</th>
                <th>Compliance Score</th>
                <th>Status</th>
                <th>Last Checked</th>
            </tr>
        </thead>
        <tbody>';
    
    while ($row = $result->fetch_assoc()) {
        $html .= '<tr>
            <td>' . $row['vessel_name'] . '</td>
            <td>' . $row['imo_number'] . '</td>
            <td>' . $row['owner_name'] . '</td>
            <td class="compliance-score">' . $row['compliance_score'] . '/10</td>
            <td>' . ucfirst($row['compliance_status']) . '</td>
            <td>' . date('Y-m-d', strtotime($row['last_checked'])) . '</td>
        </tr>';
    }
    
    $html .= '</tbody></table>';
    return $html;
}

function generateVesselReport($result) {
    $html = '
    <style>
        .report-header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; border: 1px solid #ddd; }
        th { background-color: #f5f5f5; }
    </style>
    <div class="report-header">
        <h1>Vessel Status Report</h1>
        <p>Generated on: ' . date('Y-m-d H:i:s') . '</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>Vessel Name</th>
                <th>IMO Number</th>
                <th>Owner</th>
                <th>Type</th>
                <th>Status</th>
                <th>Location</th>
            </tr>
        </thead>
        <tbody>';
    
    while ($row = $result->fetch_assoc()) {
        $html .= '<tr>
            <td>' . $row['vessel_name'] . '</td>
            <td>' . $row['imo_number'] . '</td>
            <td>' . $row['owner_name'] . '</td>
            <td>' . $row['vessel_type'] . '</td>
            <td>' . ucfirst($row['status']) . '</td>
            <td>' . ucfirst($row['location']) . '</td>
        </tr>';
    }
    
    $html .= '</tbody></table>';
    return $html;
}

function generateInspectionReport($result) {
    $html = '
    <style>
        .report-header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; border: 1px solid #ddd; }
        th { background-color: #f5f5f5; }
    </style>
    <div class="report-header">
        <h1>Vessel Inspection Report</h1>
        <p>Generated on: ' . date('Y-m-d H:i:s') . '</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>Vessel Name</th>
                <th>IMO Number</th>
                <th>Owner</th>
                <th>Inspection Date</th>
                <th>Rating</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>';
    
    while ($row = $result->fetch_assoc()) {
        $html .= '<tr>
            <td>' . $row['vessel_name'] . '</td>
            <td>' . $row['imo_number'] . '</td>
            <td>' . $row['full_name'] . '</td>
            <td>' . date('Y-m-d', strtotime($row['last_inspection_date'])) . '</td>
            <td>' . ucfirst($row['inspection_rating']) . '</td>
            <td>' . $row['approval_notes'] . '</td>
        </tr>';
    }
    
    $html .= '</tbody></table>';
    return $html;
}