<?php

// Simple database check
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=manfutsal', 'root', '');
    
    echo "Connected to database\n";
    
    // Show tables
    $stmt = $pdo->query("SHOW TABLES");
    echo "Tables:\n";
    while ($row = $stmt->fetch()) {
        echo "- " . $row[0] . "\n";
    }
    
    // Check specific tables
    echo "\nChecking payments and activities:\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = 'manfutsal' AND table_name = 'payments'");
    $result = $stmt->fetch();
    echo "Payments table exists: " . ($result['count'] > 0 ? 'YES' : 'NO') . "\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = 'manfutsal' AND table_name = 'activities'");
    $result = $stmt->fetch();
    echo "Activities table exists: " . ($result['count'] > 0 ? 'YES' : 'NO') . "\n";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
