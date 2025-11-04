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
        SELECT s.Shop_license, s.Name, s.Type, b.Purchase_date
        FROM Buys b
        JOIN Shop s ON b.Shop_license = s.Shop_license
        WHERE b.Visitor_number = ?
        ORDER BY b.Purchase_date DESC
    ");
    $stmt->bind_param("i", $visitorNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $purchases = [];
    while ($row = $result->fetch_assoc()) {
        $purchases[] = $row;
    }
    
    echo json_encode(['success' => true, 'purchases' => $purchases]);
    
    $stmt->close();
    $conn->close();
}
?>
