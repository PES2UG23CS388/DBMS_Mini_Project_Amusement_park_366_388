<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $name = $data['name'] ?? '';
    $age = $data['age'] ?? 0;
    $initialBalance = $data['initialBalance'] ?? 0;
    $isVIP = $data['isVIP'] ?? false;
    
    if (empty($name) || $age <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit;
    }
    
    try {
        $conn = getDBConnection();
        
        // Call your stored procedure
        $stmt = $conn->prepare("CALL sp_AddNewVisitor(?, ?, ?, ?)");
        $stmt->bind_param("sidi", $name, $age, $initialBalance, $isVIP);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            echo json_encode([
                'success' => true,
                'visitor_number' => $row['Visitor_Number'],
                'card_id' => $row['Card_ID'],
                'message' => 'Account created successfully!'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create account']);
        }
        
        $stmt->close();
        $conn->close();
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
