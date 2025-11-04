<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $conn = getDBConnection();
        
        if (!$conn) {
            throw new Exception('Database connection failed');
        }
        
        $stats = [];
        
        // 1. Total visitors
        $result = $conn->query("SELECT COUNT(*) as count FROM Visitor");
        if ($result) {
            $stats['total_visitors'] = $result->fetch_assoc()['count'];
        } else {
            $stats['total_visitors'] = 0;
        }
        
        // 2. Average age
        $result = $conn->query("SELECT AVG(Age) as avg FROM Visitor");
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['average_age'] = $row['avg'] ? round($row['avg'], 1) : 0;
        } else {
            $stats['average_age'] = 0;
        }
        
        // 3. VIP count
        $result = $conn->query("SELECT COUNT(*) as count FROM Card WHERE VIP = 1");
        if ($result) {
            $stats['vip_count'] = $result->fetch_assoc()['count'];
        } else {
            $stats['vip_count'] = 0;
        }
        
        // 4. Total balance
        $result = $conn->query("SELECT SUM(Balance) as total FROM Card");
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['total_balance'] = $row['total'] ? floatval($row['total']) : 0;
        } else {
            $stats['total_balance'] = 0;
        }
        
        // 5. Popular rides
        $query = "
            SELECT 
                r.Name,
                COUNT(DISTINCT e.Visitor_number) as visitors,
                AVG(f.Rating) as avg_rating
            FROM Ride r
            LEFT JOIN Enjoys e ON r.ID = e.Ride_id
            LEFT JOIN USES u ON r.ID = u.Ride_id
            LEFT JOIN Feedback f ON u.Visitor_number = f.Visitor_number 
                AND u.Feedback_number = f.Feedback_number
            GROUP BY r.ID, r.Name
            ORDER BY visitors DESC
            LIMIT 10
        ";
        
        $result = $conn->query($query);
        $popular_rides = [];
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $popular_rides[] = [
                    'Name' => $row['Name'],
                    'visitors' => $row['visitors'],
                    'avg_rating' => $row['avg_rating'] ? round($row['avg_rating'], 1) : null
                ];
            }
        }
        $stats['popular_rides'] = $popular_rides;
        
        // 6. Popular restaurants
        $query = "
            SELECT 
                Eats_at as Name,
                COUNT(*) as visitors
            FROM Visitor
            WHERE Eats_at IS NOT NULL AND Eats_at != ''
            GROUP BY Eats_at
            ORDER BY visitors DESC
            LIMIT 10
        ";
        
        $result = $conn->query($query);
        $popular_mess = [];
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $popular_mess[] = [
                    'Name' => $row['Name'],
                    'visitors' => $row['visitors']
                ];
            }
        }
        $stats['popular_mess'] = $popular_mess;
        
        echo json_encode([
            'success' => true,
            'statistics' => $stats
        ]);
        
        $conn->close();
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error loading statistics: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>
