<?php
require_once '../config/database.php';

session_unset();
session_destroy();

echo json_encode([
    'success' => true,
    'message' => 'Logged out successfully'
]);
?>
