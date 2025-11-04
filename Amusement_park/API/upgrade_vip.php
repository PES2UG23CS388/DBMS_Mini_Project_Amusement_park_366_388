<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $visitorNumber = $data['visitorNumber'] ?? 0;
    
    if ($visitorNumber <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid visitor number']);
        exit;
    }
    
    $conn = getDBConnection();
    
    // Check current balance
    $stmt = $conn->prepare("SELECT Balance, VIP FROM Card WHERE Visitor_number = ?");
    $stmt->bind_param("i", $visitorNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    $card = $result->fetch_assoc();
    
    if ($card['VIP']) {
        echo json_encode(['success' => false, 'message' => 'Already a VIP member']);
        exit;
    }
    
    $vipCost = 1500.00;  // ← CHANGED from 500.00
    
    if ($card['Balance'] < $vipCost) {
        echo json_encode(['success' => false, 'message' => 'Insufficient balance. VIP upgrade costs ₹1500']);
        exit;
    }
    
    // Upgrade to VIP
    $updateStmt = $conn->prepare("UPDATE Card SET VIP = true, Balance = Balance - ? WHERE Visitor_number = ?");
    $updateStmt->bind_param("di", $vipCost, $visitorNumber);
    
    if ($updateStmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Successfully upgraded to VIP!',
            'new_balance' => $card['Balance'] - $vipCost
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Upgrade failed']);
    }
    
    $stmt->close();
    $updateStmt->close();
    $conn->close();
}
?>
