<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $conn = getDBConnection();
    
    // Get mess with visitor count
    $query = "
        SELECT 
            m.Name, 
            m.Price, 
            m.Vegetarian,
            COUNT(v.Number) as Visitor_Count
        FROM Mess m
        LEFT JOIN Visitor v ON m.Name = v.Eats_at
        GROUP BY m.Name, m.Price, m.Vegetarian
        ORDER BY m.Name
    ";
    
    $result = $conn->query($query);
    
    $mess = [];
    while ($row = $result->fetch_assoc()) {
        $mess[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'mess' => $mess
    ]);
    
    $conn->close();
}
?>
