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
    
    $stmt = $conn->prepare("SELECT Number, Name, Age, Eats_at FROM Visitor WHERE Number = ?");
    $stmt->bind_param("i", $visitorNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $visitor = $result->fetch_assoc();
        $_SESSION['visitor_number'] = $visitor['Number'];
        $_SESSION['visitor_name'] = $visitor['Name'];
        
        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'visitor' => $visitor
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Visitor not found']);
    }
    
    $stmt->close();
    $conn->close();
}
?>
