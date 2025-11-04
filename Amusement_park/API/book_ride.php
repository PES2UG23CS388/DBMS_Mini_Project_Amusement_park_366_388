<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $visitorNumber = $data['visitorNumber'] ?? 0;
    $rideID = $data['rideID'] ?? 0;
    
    if ($visitorNumber <= 0 || $rideID <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit;
    }
    
    $conn = getDBConnection();
    
    // Call stored procedure
    $stmt = $conn->prepare("CALL sp_BookRide(?, ?)");
    $stmt->bind_param("ii", $visitorNumber, $rideID);
    
    try {
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            echo json_encode([
                'success' => true,
                'message' => $row['message'],
                'new_balance' => $row['new_balance']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Booking failed: ' . $stmt->error]);
        }
    } catch (Exception $e) {
        // Extract the actual error message
        $errorMsg = $e->getMessage();
        if (strpos($errorMsg, 'VIP membership required') !== false) {
            echo json_encode(['success' => false, 'message' => 'VIP membership required for this ride']);
        } else if (strpos($errorMsg, 'Insufficient balance') !== false) {
            echo json_encode(['success' => false, 'message' => 'Insufficient balance']);
        } else {
            echo json_encode(['success' => false, 'message' => $errorMsg]);
        }
    }
    
    $stmt->close();
    $conn->close();
}
?>
