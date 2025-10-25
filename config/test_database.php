<?php
require_once 'database.php'; // make sure the path is correct

try {
    // Try getting the PDO connection
    $db = getDB()->getConnection();
    
    // Simple query to test connection
    $result = dbFetchOne("SELECT NOW() AS current_time");
    
    echo "✅ Database connected successfully!<br>";
    echo "Current database time: " . $result['current_time'];
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage();
}
?>
