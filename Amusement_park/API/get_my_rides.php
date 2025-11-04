<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $visitorNumber = $_GET['visitor_number'] ?? 0;
    
    if ($visitorNumber <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid visitor number']);
        exit;
    }
    
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("
        SELECT r.ID, r.Name, r.Cost, e.Enjoy_date
        FROM Enjoys e
        JOIN Ride r ON e.Ride_id = r.ID
        WHERE e.Visitor_number = ?
        ORDER BY e.Enjoy_date DESC
    ");
    $stmt->bind_param("i", $visitorNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $rides = [];
    while ($row = $result->fetch_assoc()) {
        $rides[] = $row;
    }
    
    echo json_encode(['success' => true, 'rides' => $rides]);
    
    $stmt->close();
    $conn->close();
}
?>
