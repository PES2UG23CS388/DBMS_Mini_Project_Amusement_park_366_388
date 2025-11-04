<?php
require_once '../config/database.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $visitorNumber = $_GET['visitor_number'] ?? null;
    
    if (!$visitorNumber) {
        echo json_encode(['success' => false, 'message' => 'Visitor number required', 'rides' => []]);
        exit;
    }
    
    $conn = getDBConnection();
    
    try {
        // Get ONLY rides this visitor has enjoyed
        $stmt = $conn->prepare("
            SELECT DISTINCT
                r.ID as ride_id,
                r.Name as ride_name
            FROM Enjoys e
            JOIN Ride r ON e.Ride_id = r.ID
            WHERE e.Visitor_number = ?
            ORDER BY r.Name
        ");
        
        $stmt->bind_param("i", $visitorNumber);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $rides = [];
        while ($row = $result->fetch_assoc()) {
            $rides[] = [
                'rideID' => (int)$row['ride_id'],
                'rideName' => $row['ride_name']
            ];
        }
        
        echo json_encode([
            'success' => true,
            'rides' => $rides
        ]);
        
        $stmt->close();
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage(),
            'rides' => []
        ]);
    }
    
    $conn->close();
}
?>
