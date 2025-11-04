<?php
require_once '../config/database.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $visitorNumber = $_GET['visitor_number'] ?? null;
    
    if (!$visitorNumber) {
        echo json_encode(['success' => false, 'message' => 'Visitor number required']);
        exit;
    }
    
    $conn = getDBConnection();
    
    try {
        // Calculate total spent on rides
        $stmt = $conn->prepare("
            SELECT COALESCE(SUM(r.Cost), 0) as total_spent
            FROM Enjoys e
            JOIN Ride r ON e.Ride_id = r.ID
            WHERE e.Visitor_number = ?
        ");
        
        $stmt->bind_param("i", $visitorNumber);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        echo json_encode([
            'success' => true,
            'total_spent' => (float)$row['total_spent']
        ]);
        
        $stmt->close();
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
    
    $conn->close();
}
?>
