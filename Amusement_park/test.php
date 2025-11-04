<?php
require_once 'config/database.php';

echo "<h2>🎢 Funderland - Database Connection Test</h2>";

$conn = getDBConnection();

if ($conn) {
    echo "<p style='color: green;'>✅ Connected successfully to database: " . DB_NAME . "</p>";
    
    // Test Visitor count
    $result = $conn->query("SELECT COUNT(*) as count FROM Visitor");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p>✅ Number of visitors: <strong>" . $row['count'] . "</strong></p>";
    }
    
    // Test Ride count
    $result = $conn->query("SELECT COUNT(*) as count FROM Ride");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p>✅ Number of rides: <strong>" . $row['count'] . "</strong></p>";
    }
    
    // Test procedures
    $result = $conn->query("SHOW PROCEDURE STATUS WHERE Db = 'amusement_park'");
    echo "<p>✅ Stored procedures found: <strong>" . $result->num_rows . "</strong></p>";
    
    $conn->close();
    
    echo "<hr>";
    echo "<p style='color: green; font-size: 20px;'>🎉 <strong>Everything is working!</strong></p>";
    echo "<p><a href='pages/index.html' style='padding: 10px 20px; background: #ff6b6b; color: white; text-decoration: none; border-radius: 5px;'>Go to Application →</a></p>";
    
} else {
    echo "<p style='color: red;'>❌ Connection failed!</p>";
}
?>
