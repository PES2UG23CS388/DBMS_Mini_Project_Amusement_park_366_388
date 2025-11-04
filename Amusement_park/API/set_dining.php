<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $visitorNumber = $data['visitorNumber'] ?? 0;
    $messName = $data['messName'] ?? '';
    
    if ($visitorNumber <= 0 || empty($messName)) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit;
    }
    
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("UPDATE Visitor SET Eats_at = ? WHERE Number = ?");
    $stmt->bind_param("si", $messName, $visitorNumber);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Dining preference updated successfully'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Update failed: ' . $stmt->error]);
    }
    
    $stmt->close();
    $conn->close();
}
?>
