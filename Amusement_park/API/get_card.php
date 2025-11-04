<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $visitorNumber = $_GET['visitor_number'] ?? 0;
    
    if ($visitorNumber <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid visitor number']);
        exit;
    }
    
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("SELECT Card_id, Balance, VIP FROM Card WHERE Visitor_number = ?");
    $stmt->bind_param("i", $visitorNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $card = $result->fetch_assoc();
        echo json_encode(['success' => true, 'card' => $card]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Card not found']);
    }
    
    $stmt->close();
    $conn->close();
}
?>
