<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $visitorNumber = $data['visitorNumber'] ?? 0;
    $shopLicense = $data['shopLicense'] ?? '';
    
    if ($visitorNumber <= 0 || empty($shopLicense)) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit;
    }
    
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("INSERT INTO Buys (Visitor_number, Shop_license) VALUES (?, ?)");
    $stmt->bind_param("is", $visitorNumber, $shopLicense);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Purchase recorded successfully'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Purchase failed: ' . $stmt->error]);
    }
    
    $stmt->close();
    $conn->close();
}
?>
