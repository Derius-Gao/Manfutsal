<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

try {
    // Test database connection
    echo "Testing database connection...\n";
    
    // Check if tables exist
    $tables = DB::select('SHOW TABLES');
    echo "Tables in database:\n";
    foreach($tables as $table) {
        echo "- " . $table->Tables_in_manfutsal . "\n";
    }
    
    // Check specific tables
    echo "\nChecking specific tables:\n";
    echo "Payments table exists: " . (Schema::hasTable('payments') ? 'YES' : 'NO') . "\n";
    echo "Activities table exists: " . (Schema::hasTable('activities') ? 'YES' : 'NO') . "\n";
    
    // Count records
    echo "\nRecord counts:\n";
    echo "Users: " . DB::table('users')->count() . "\n";
    echo "Lapangan: " . DB::table('lapangan')->count() . "\n";
    echo "Bookings: " . DB::table('bookings')->count() . "\n";
    echo "Payments: " . DB::table('payments')->count() . "\n";
    echo "Activities: " . DB::table('activities')->count() . "\n";
    
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
