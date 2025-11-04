<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $visitorNumber = $data['visitorNumber'] ?? 0;
    $amount = $data['amount'] ?? 0;
    
    if ($visitorNumber <= 0 || $amount <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit;
    }
    
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("CALL sp_RechargeCard(?, ?)");
    $stmt->bind_param("id", $visitorNumber, $amount);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        echo json_encode([
            'success' => true,
            'message' => 'Card recharged successfully',
            'new_balance' => $row['Balance']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Recharge failed']);
    }
    
    $stmt->close();
    $conn->close();
}
?>
