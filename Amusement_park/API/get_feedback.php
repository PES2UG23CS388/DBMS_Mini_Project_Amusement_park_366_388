<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $visitorNumber = $_GET['visitor_number'] ?? null;
    
    $conn = getDBConnection();
    
    if ($visitorNumber) {
        // Get feedback for specific visitor
        $stmt = $conn->prepare("
            SELECT 
                f.Visitor_number, 
                f.Feedback_number, 
                f.Rating, 
                f.Review, 
                r.Name as Ride_Name, 
                v.Name as Visitor_Name
            FROM Feedback f
            JOIN USES u ON f.Visitor_number = u.Visitor_number 
                AND f.Feedback_number = u.Feedback_number
            JOIN Ride r ON u.Ride_id = r.ID
            JOIN Visitor v ON f.Visitor_number = v.Number
            WHERE f.Visitor_number = ?
            ORDER BY f.Feedback_number DESC
        ");
        $stmt->bind_param("i", $visitorNumber);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        // Get all feedback
        $result = $conn->query("
            SELECT 
                f.Visitor_number, 
                f.Feedback_number, 
                f.Rating, 
                f.Review, 
                r.Name as Ride_Name, 
                v.Name as Visitor_Name
            FROM Feedback f
            JOIN USES u ON f.Visitor_number = u.Visitor_number 
                AND f.Feedback_number = u.Feedback_number
            JOIN Ride r ON u.Ride_id = r.ID
            JOIN Visitor v ON f.Visitor_number = v.Number
            ORDER BY f.Feedback_number DESC
            LIMIT 50
        ");
    }
    
    $feedback = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $feedback[] = $row;
        }
    }
    
    echo json_encode([
        'success' => true,
        'feedback' => $feedback
    ]);
    
    if (isset($stmt)) $stmt->close();
    $conn->close();
}
?>
