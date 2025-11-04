<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $messName = $_GET['mess_name'] ?? '';
    
    if (empty($messName)) {
        echo json_encode(['success' => false, 'message' => 'Mess name required']);
        exit;
    }
    
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("SELECT Item FROM Mess_Food WHERE Name = ?");
    $stmt->bind_param("s", $messName);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row['Item'];
    }
    
    echo json_encode(['success' => true, 'items' => $items]);
    
    $stmt->close();
    $conn->close();
}
?>
