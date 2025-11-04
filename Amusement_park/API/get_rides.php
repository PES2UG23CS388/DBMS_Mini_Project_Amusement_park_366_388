<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $conn = getDBConnection();
    
    // Simple query first - just get rides
    $query = "SELECT ID, Name, VIP, Cost FROM Ride ORDER BY Name";
    
    $result = $conn->query($query);
    
    if (!$result) {
        echo json_encode([
            'success' => false,
            'message' => 'Query error: ' . $conn->error
        ]);
        exit;
    }
    
    $rides = [];
    while ($row = $result->fetch_assoc()) {
        // Add default values for counts
        $row['Total_Visitors'] = 0;
        $row['Average_Rating'] = 0;
        $rides[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'rides' => $rides
    ]);
    
    $conn->close();
}
?>
