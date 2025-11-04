<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $conn = getDBConnection();
    
    // Get shops with purchase count and owner name
    $query = "
    SELECT 
        s.Shop_license, 
        s.Name, 
        s.Open, 
        s.Type,
        e.Name as Owner_Name,
        COUNT(b.Visitor_number) as Purchase_Count
    FROM Shop s
    LEFT JOIN Employee e ON s.Owner_SSN = e.SSN
    LEFT JOIN Buys b ON s.Shop_license = b.Shop_license
    GROUP BY s.Shop_license, s.Name, s.Open, s.Type, e.Name
    ORDER BY s.Name
";
    
    $result = $conn->query($query);
    
    $shops = [];
    while ($row = $result->fetch_assoc()) {
        $shops[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'shops' => $shops
    ]);
    
    $conn->close();
}
?>
