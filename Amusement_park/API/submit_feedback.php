<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $visitorNumber = $data['visitorNumber'] ?? 0;
    $rideID = $data['rideID'] ?? 0;
    $rating = $data['rating'] ?? 0;
    $review = $data['review'] ?? '';
    
    if ($visitorNumber <= 0 || $rideID <= 0 || $rating <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit;
    }
    
    if ($rating < 1 || $rating > 5) {
        echo json_encode(['success' => false, 'message' => 'Rating must be between 1 and 5']);
        exit;
    }
    
    $conn = getDBConnection();
    
    try {
        // Call stored procedure
        $stmt = $conn->prepare("CALL sp_SubmitFeedback(?, ?, ?, ?)");
        $stmt->bind_param("iiis", $visitorNumber, $rideID, $rating, $review);
        
        if ($stmt->execute()) {
            // Get the result
            $result = $stmt->get_result();
            if ($result && $row = $result->fetch_assoc()) {
                echo json_encode([
                    'success' => true,
                    'message' => $row['message'] ?? 'Feedback submitted successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'message' => 'Feedback submitted successfully'
                ]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to submit feedback: ' . $stmt->error]);
        }
        
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    
    $conn->close();
}
?>
