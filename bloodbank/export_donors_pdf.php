<?php
require_once('tcpdf/tcpdf.php'); // Adjust path if needed
include('db_connection.php');

if (!isset($_GET['group_id'])) {
    die("Blood group not specified.");
}

$group_id = intval($_GET['group_id']);

// Fetch blood group name
$stmt = $conn->prepare("SELECT BloodGroup FROM bloodgroup WHERE BloodGroupID = ?");
$stmt->bind_param("i", $group_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Invalid blood group ID.");
}
$group = $result->fetch_assoc();
$blood_group_name = $group['BloodGroup'];

// Fetch donors in that blood group
$stmt = $conn->prepare("SELECT * FROM donor WHERE BloodGroupID = ?");
$stmt->bind_param("i", $group_id);
$stmt->execute();
$donors = $stmt->get_result();

// Create PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Blood Bank Management');
$pdf->SetTitle("Donor List - $blood_group_name");
$pdf->SetMargins(15, 40, 15);
$pdf->AddPage();

// Header with logo and title
$logo = 'http://localhost/bloodbank/Project%20logo.png';
 // Make sure this path is correct
$headerHTML = '
    <table style="width:100%;">
        <tr>
            <td style="width:20%;">
                <img src="' . $logo . '" height="60">
            </td>
            <td style="width:80%; text-align:center;">
                <h2>Blood Bank Management System</h2>
                <h4>Donor List - Blood Group: ' . htmlspecialchars($blood_group_name) . '</h4>
            </td>
        </tr>
    </table>
    <hr>
';
$pdf->writeHTML($headerHTML, true, false, true, false, '');

// Table of donors
$html = '
<table border="1" cellpadding="5">
    <thead>
        <tr style="background-color:#f2f2f2;">
            <th><b>Donor ID</b></th>
            <th><b>Name</b></th>
            <th><b>Age</b></th>
            <th><b>Gender</b></th>
            <th><b>Phone</b></th>
            <th><b>Address</b></th>
            <th><b>Last Donation</b></th>
        </tr>
    </thead>
    <tbody>
';

while ($donor = $donors->fetch_assoc()) {
    $html .= '
        <tr>
            <td>D' . str_pad($donor['DonorID'], 2, '0', STR_PAD_LEFT) . '</td>
            <td>' . htmlspecialchars($donor['Name']) . '</td>
            <td>' . htmlspecialchars($donor['Age']) . '</td>
            <td>' . htmlspecialchars($donor['Gender']) . '</td>
            <td>' . htmlspecialchars($donor['Phone_No']) . '</td>
            <td>' . htmlspecialchars($donor['Address']) . '</td>
            <td>' . htmlspecialchars($donor['LastDonationDate']) . '</td>
        </tr>';
}

if ($donors->num_rows === 0) {
    $html .= '<tr><td colspan="7" align="center">No donors found.</td></tr>';
}

$html .= '</tbody></table>';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('donors_' . $blood_group_name . '.pdf', 'I'); // I = inline display
?>
